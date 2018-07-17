

import ij.*;
import ij.process.*;
import ij.gui.*;
import ij.measure.*;
import ij.plugin.frame.RoiManager;
import java.awt.*;
import ij.plugin.filter.*;
import ij.measure.ResultsTable;



/* 	Author: Francesco sisini 
	based in part on code from Slicer.java in ImageJ
	Contact: Francesco Sisini ssf [at] unife.it
	Licence: Public Domain	*/
	

/**
   WARNING: this plugin is not thread safe!!!
*/


public class USJD_20 implements PlugInFilter {

   
   
   private boolean suppress,centre; 
    
    private double outputZSpacing = 1.0;
    private int nprofiles = 1;
    private boolean noRoi;
    private ImagePlus imp;

    //The roi follower USJD
    /**
       La procedura richiede di tracciare sulla prima immagine del video
       il contorno (ROI) della vena di cui si vuoleseguire la pulsazione.
       Dopo avre trattciato la ROI questa va aggiunta al ROI manager.
       Si deve poi tracciare il semi asse maggiore della ipotetica ellissi 
       in cui la ROI e' iscritta. Il seminasse va quindi aggiunto al ROI manager.
       L'algoritmo esegue una scansione di tutto lo stack di immagini tracciando
       lo spostamento dei punti della ROI iniziale basandosi sulla cross coreelazione
       del profilo acquisito su un'immagine con il profilo acquisito su una immagine successiva.
     **/
   
    RoiManager manager;	
    int udr[],udrr[];
    private  double[]  xrc,yrc;//Coordinate del centro della ROI iniziale
   // private double elAngle=0;//L'angolo che l'asse orrizontale dell'elisse forma con l'asse delle x misurato in senso orario 
    private double alphaCC, deltaAlpha;
    private double [][] x;//Coordinate x e y della ROI calcolata per ogni frame del filamto. Il primo indice 
    private double [][] y;//si riferisce all'immagine nel filamto il secondo all'indice all'angolo rispetto al centro.
    private double[] myang;//L'angolo di scansione della CrossCorrelation. L'inice corrisponde al secondo indice delle coordinate
    private int[][] delay;//il ritardo nella CC misurato per ogni immagine e ogni  angolo
    private float[] rrd,frrd;
    float prrd[];
    private int[][] filtDelay;//i dely con filtro median
    private float[] fdelay;
    double[][] avdelay;// il ritardo mediato su un campione di sampleXarc angoli. (stessi indici di sopra)
    double cave[][];//il ritardo ripulito
    double[][]ns;
    double[][]sd;//la deviazione standard del campione al dato angolo
    double aAx=0,bAx=0;
    int caz=0;
    int[][] l;//Array con la distanza in pixel dal centro al contorno della ROI iniziale
    int currentRoi=0;
    int udrs=0;
    //( per esempio se frameDistance=2 si confrontano la 1 e la 3, la 3 e la 5...)
    private double mag=1.;
    private int mediaRange=10;//il semi intervalo usato per applicare il filtro medio ai valori dei riatrdi
    double outerOscillationLimit=10; //Range espansione massimo del raggio in pixel
    double innerOscillationLimit=10;
    Roi[] mainROI;
    int mstackSize;
    int maxdel=200;
    float M[];//ddr Metric

    public int setup(String arg, ImagePlus imp) {
	
	return DOES_ALL;
    }
    
    
    public void run(ImageProcessor ip) {
	
	imp = WindowManager.getCurrentImage();
	ImageStack stack = imp.getStack();
	mstackSize = stack.getSize();
	udr=new int[mstackSize];
	manager = RoiManager.getInstance(); 
	Roi[] ra=manager.getRoisAsArray();
	
	/**
	 * Produzione arrai udr
	 */
	udrs=ra.length; //Numero di ROI preselezionate dall'utente
	udrr=new int[udrs];
	mainROI=new Roi[udrs];
	xrc=new double[udrs];
	yrc=new double[udrs];
	
	int ui,mr=0;
	for( ui=0;ui<udrs;ui++)
	{
		String name=ra[ui].getName();
		mr=Integer.parseInt(name.split("-")[0]);
		
		for(int gg=mr-1;gg<mstackSize;gg++)
		{
			udr[gg]=ui;
			udrr[ui]=mr-1;
			IJ.log(gg+":"+udr[gg]);
		}
		
	}
//	for(int gg=counter;gg<mstackSize;gg++)
//	{
//		udr[gg]=ui;
//		IJ.log(gg+":"+udr[gg]);
//	}
	
	
	if (ra.length<1)
	    {
		IJ.error("Selezionare la ROI della IJV su almeno un'immagine.");
		return;
	    }
	for(int i=0;i<ra.length;i++)
	{
		mainROI[i] = ra[i];
	}
	
	if(mainROI==null)IJ.showMessage("Nessuna ROI selezionata");
	Polygon fp=mainROI[currentRoi].getPolygon();

	
	if (imp==null) {
	    IJ.noImage();
	    return;
	}
	int stackSize = imp.getStackSize();
	Roi roi = imp.getRoi();
	int roiType = roi!=null?roi.getType():0;
	if (stackSize<2) {
	    IJ.error("USJD:", "Stack required");
	    return;
	}
	if (!showDialog(imp))	//need to update the dialog
	    return;
	long startTime = System.currentTimeMillis();	
	for(int kk=0;kk<udrs;kk++){
		currentRoi=kk;
		radslice(mainROI);
	}
	currentRoi=0;

	    
	/**
	   Cross correlation between signal acquired in two differet frames 
	*/
	
	int n=stack.getSize();
	int z=nprofiles;
	
	/**
	   STEP 1 crea l'array dei ritardi
	 */
	populateDelayArray( n,z,stack);
	
	/**
	   STEP 2 correzioni statistiche e Filtri 
	 */
	doRejectionData();
	doAverageFilterToDelay();
	
	
	ImagePlus ipp=NewImage.createFloatImage("Delay", z, n, 1, 1);
	ipp.getProcessor().setPixels(fdelay);
	ipp.show();
	

	//ImagePlus ppm=NewImage.createFloatImage("rrd()", z/2, n, 1, 1);
	//ppm.getProcessor().setPixels(prrd);
	//ppm.show();
	
//	ImagePlus ippm=NewImage.createFloatImage("M metrics", 1, n, 1, 1);
//	ippm.getProcessor().setPixels(M);
//	ippm.show();

	/**
	   STEP 3 ricalcola la ROI sui ritardi
	 */
	applyDelaysToROI( n, z);
	logDelayRatio(n, z);
	int rep= (int) Math.round(360./deltaAlpha);
	//JVP totale
	measureROIs(0,360);
	for(int inx=0;inx<rep;inx++)
	{	
		double a=alphaCC+inx*deltaAlpha;
		IJ.log("Analizzo JVP da:"+a+" a "+(a+deltaAlpha));
		measureROIs(a,a+deltaAlpha );
	}
	if (noRoi)
	    imp.killRoi();
	else
	    imp.draw();
	IJ.showStatus(IJ.d2s(((System.currentTimeMillis()-startTime)/1000.0),2)+" seconds");

    }



    
    /*
     * Produce e misura le ROI relative ai settori ancgolari da a incluso a b escluso
     */
    void measureROIs(double alp_i, double alp_f)
    {

    	if(alp_i<0)alp_i=alp_i+360;
    	if(alp_f<0)alp_f=alp_f+360;
    	if(alp_i>360)alp_i=alp_i-360;
    	if(alp_f>360)alp_f=alp_f-360;
    	
    	//Trasformo gli angoli in indici
    	int i=(int)Math.round( alp_i*nprofiles/360.);
    	int f=(int)Math.round(alp_f*nprofiles/360.);
    	//IJ.log("Calcolo ROI da "+i+" a "+f );
    	
    	if(i!=f){
    			for(int mt=0;mt<mstackSize;mt++)
    			{
    				currentRoi=udr[mt]-1;
    				caz++;
    				int[] xr=new int[nprofiles];
    				int[] yr=new int[nprofiles];
    				
	    			if(i<f)
	    				
	    			{
	    					
	    				//ciclo su tutto lo stack
	    				/*for(int mu=0;mu<nprofiles;mu++){
	    			    	 xr[mu]=(int)x[0][mu];
	    					 yr[mu]=(int)y[0][mu];
	    			    }*/
	    					
	    			    for(int mu=i;mu<f;mu++){
	    			    	
	    			    	 xr[mu]=(int)x[mt][mu];
	    					 yr[mu]=(int)y[mt][mu];
	    			    }
	    			    
	    			}	else   			{
	    				//IJ.log("Scarto caortide da: "+i+" incluso  a "+f+" escluso passando per lo 0");
	    				
	    				for(int mu=0;mu<nprofiles;mu++){
	    			    	 xr[mu]=(int)x[mt][mu];
	    					 yr[mu]=(int)y[mt][mu];
	    			    }
	    				for(int mu=0;mu<f;mu++){
	    					xr[mu]=(int)x[mt][mu];
    				    	yr[mu]=(int)y[mt][mu];
    				    }
	    				for(int mu=i;mu<nprofiles;mu++){
	    				    	xr[mu]=(int)x[mt][mu];
	    				    	yr[mu]=(int)y[mt][mu];
	    				    }
	    				
	    			}
	    			ResultsTable rt=ResultsTable.getResultsTable();
    				PolygonRoi myROI= new PolygonRoi(xr, yr,xr.length, Roi.POLYGON);
    				imp.setSliceWithoutUpdate(mt+1);
    				manager.add(imp,myROI,caz);
    				
    				imp.setRoi(myROI);
    				
    				//Da chiarire
    				Analyzer a=new Analyzer(imp);
    				ImageStatistics ist=imp.getStatistics();
    				a.saveResults(ist,myROI);
    				rt.show("Results");
    			}
    	}
    				   		
    	
    }
    

    /*
     * Scarto dei valori in base alla loro probabilità su una distribuzione gaussiana (chauvelier)
     */
    public void doRejectionData()
    {
    	//Ciclo su tutti gli angoli
    	
    	for(int i=0;i<nprofiles;i++)
	    {	
    		//calcolo la dimensione del campione in base all'indice dell'array
    		
    		
    		//Calcolo la media e la sd relative a questo profilo
    		double v[]=new double[mstackSize];
    		for(int j=0;j<mstackSize;j++) v[j]=delay[j][i];
    		double stat[]=absMean(v);
    		for(int j=0;j<mstackSize;j++){
    			double p=Math.abs(Math.abs(delay[j][i])-stat[0])/stat[1];
    			if(p>4)
    			{
    			delay[j][i]= new Double( stat[0]*Math.signum(  (delay[j][i]))).intValue();
    			
    			//IJ.log("Rejection: slice"+j+" valore"+delay[j][i]);
    			}
    		}
    		}
    		
    	
    }
    public void doMedianFilterToddrArray()
    {
    	int range= mediaRange;
    	float[] vals=new float[mediaRange];
    	
    		for(int j=0;j<nprofiles/2;j++)
    		    {
    			
    			int s=0;
    			for(int k=-range/2;k<range/2;k++)
    			    {
    				int ri=j+k;
    				if(ri<0)
    				{
    					vals[s]=rrd[0];
    				}else if(ri>=nprofiles/2)
    				{
    					vals[s]=rrd[nprofiles/2-1];	
    				}else 
    				{
    					vals[s]=rrd[ri];
    				}
    				s++;
    			    }
    			java.util.Arrays.sort(vals);
    			frrd[j]=vals[mediaRange/2];
    		    }
    		rrd=frrd;

    	    
    	

    }
    
    //Applica un filtro del valor medio all'array dei ritardi
    public void doAverageFilterToDelay()
    {
	
    int range= mediaRange;
	for(int i=0;i<mstackSize;i++)
	    {
		for(int j=0;j<nprofiles;j++)
		    {
			double vm=0;
			for(int k=-range;k<range;k++)
			    {
				int ri=j+k;
				if(ri<0)ri=nprofiles+ri;
				if(ri>=nprofiles)ri=ri-nprofiles;
				vm=vm+delay[i][ri];
			    }
			
			filtDelay[i][j]=(int)vm/(range+1);
			fdelay[i*nprofiles+j]=(float)filtDelay[i][j];
			
		    }
		

	    }
	delay=filtDelay;

    }

    


   /*
    * Crea l'array dei ritardi per ogni angolo 
    */
    public void populateDelayArray(int n,  int z,ImageStack is){
	//n: immagini nello stack
	//r:
	//z:
	
	//mu:=indice angolo 
	//mt:=indice immagine (istante di acquisizione)
	//mz:=distanza dal centro della ROI
    	
    for(int mt=1;mt<n-1;mt++)
    {
    	currentRoi=udr[mt];
    	//Ciclo su 2PI
    	for(int mu=1;mu<=z;mu++)
    	{
    		/* Calcolo il raggio ellittico */
    		double hth=myang[mu-1];
    		int xf=(int)Math.round( xrc[currentRoi]+(l[currentRoi][mu-1]+outerOscillationLimit)*(Math.cos(hth)));
    		int yf=(int)Math.round( yrc[currentRoi]+(l[currentRoi][mu-1]+outerOscillationLimit)*(Math.sin(hth)));
    		int xi=(int)Math.round( xrc[currentRoi]+(l[currentRoi][mu-1]-innerOscillationLimit)*(Math.cos(hth)));
    		int yi=(int)Math.round( yrc[currentRoi]+(l[currentRoi][mu-1]-innerOscillationLimit)*(Math.sin(hth)));
    		Line ln=new Line(xi,yi,xf,yf);
    		drawLine(xi, yi, xf, yf, imp);
    		
    		imp.setSlice(udrr[currentRoi]+1);
    		//imp.setSlice(1);
    		imp.setRoi(ln,true);
    		double[] s1=ln.getPixels();
    		
    		//Ottengo i segnali a t1 e tn
			imp.setSlice(mt+1);
			imp.setRoi(ln,true);		
			double[] s2=ln.getPixels();
			int mdel=0;
			mdel=crossCorrelationDelay(s1, s2);
			delay[mt][mu-1]=(int)mdel;	
			if((mu-1)>=z/2){
				rrd[(mu-1)-z/2]=delay[mt][mu-1]-delay[mt][(mu-1)-z/2];
				M[mt]=M[mt]+rrd[(mu-1)-z/2]/(z/2);
			}
		    
			
	    }
    	doMedianFilterToddrArray();
		for(int k=0;k<nprofiles/2;k++)
			prrd[mt*(nprofiles/2)+k]=rrd[k];
	}
    }
    
   /**Calcola la lunghezza del raggio di un ellisse di assi aAx e bAx corrispondente
  		all'angolo th misurato in senso orario rispetto all'asse maggiore.
    */
//    public double[] ellipticalRadious(double th)
//    {
//	double a=aAx;
//	double b=bAx;
//	double nx=(a*Math.cos(th))*Math.cos(elAngle)-(b*Math.sin(th))*Math.sin(elAngle);
//	double ny=(a*Math.cos(th))*Math.sin(elAngle)+(b*Math.sin(th))*Math.cos(elAngle);
//	double rel=Math.sqrt(nx*nx+ny*ny);
//	double ret[]=new double[3];
//	ret[1]=nx+xrc[currentRoi];ret[2]=ny+yrc[currentRoi];ret[0]=rel;
//	return ret;
//	
//    }

    /*
     * Calcola la nuova roi
     */
    
    public void applyDelaysToROI(int n, int z)
	{
    	
	    for(int mt=0;mt<n-1;mt++)
		{//ciclo su tutto lo stack
	    	//ricalcolo la ROI solo se non è user defined
	    	if(!((mt+1)==udrr[udr[mt+1]]))
	    	{
			    for(int mu=1;mu<=z;mu++){
					double dd=delay[mt][mu-1];
					x[mt+1][mu-1]=x[udrr[udr[mt]]][mu-1]+((Math.cos(myang[mu-1])*dd)*mag);
					y[mt+1][mu-1]=y[udrr[udr[mt]]][mu-1]+((Math.sin(myang[mu-1])*dd)*mag);	
			    }
	    	}
	    	else
	    	{
	    		IJ.log("Salto ROI "+(mt+1));
	    		
	    	}
		}
	}
    public void logDelayRatio(int n, int z)
    {
    	IJ.log("Rapporto");
    	for(int mt=0;mt<n-1;mt++)
		{//ciclo su tutto lo stack
    		int inx1=new Double(1./6.*z).intValue();
    		int inx2=new Double(2./6.*z).intValue();
    		int inx3=new Double(3./6.*z).intValue();
    		int inx4=new Double(4./6.*z).intValue();
    		int inx5=new Double(5./6.*z).intValue();
    		double q1=delay[mt][inx1];
    		double q2=delay[mt][inx2];
    		double q3=delay[mt][inx3];
    		double q4=delay[mt][inx4];
    		double q5=delay[mt][inx5];
    		
    			double r1;
    			double r2;
    			double r3;
    			double r4;
    			double r5;
    			
    			String str="";
    			if(q1!=0){
    				r1=delay[mt][0]/q1;
    				str+=(new Double(r1)).toString().replace(".", ",")+";";
    			}else str+=";";
    			if(q2!=0){
    				r2=delay[mt][0]/q2;
    				str+=(new Double(r2)).toString().replace(".", ",")+";";
    			}else str+=";";
    			if(q3!=0){
    				r3=delay[mt][0]/q3;
    				str+=(new Double(r3)).toString().replace(".", ",")+";";
    			}
    			else str+=";";
    			if(q4!=0){
    				r4=delay[mt][0]/q4;
    				str+=(new Double(r4)).toString().replace(".", ",")+";";
    			}else str+=";";
    			if(q5!=0){
    				r5=delay[mt][0]/q5;
    				str+=(new Double(r5)).toString().replace(".", ",")+";";
    			}else str+=";";
    			
    			
    			
    			
    			//IJ.log(mt+" "+str);
    		
		}
    }

    int findMax(double[] x,double[] y)
    {
    	int ix=0,iy=0;
    	double mx=0,my=0;
    	//Calcolo il gradiente
    	double[] gx=new double[x.length];
    	double[] gy=new double[y.length];
    	for(int i=0;i<x.length-1;i++)
    	{
    		gx[i]=x[i+1]-x[i];
    	}
    	for(int i=0;i<y.length-1;i++)
    	{
    		gy[i]=y[i+1]-y[i];
    	}
    	
    	for(int i=0;i<x.length;i++)
    	{
    		if(x[i]>mx){
    			ix=i;
    			mx=x[i];
    		}
    		if(y[i]>my){
    			iy=i;
    			my=y[i];
    		}
    	}
    	return iy-ix;
    	
    }
    
    int crossCorrelationDelay(double[] x,double[] y )
    { 
	double mr;
	int mdelay=-100;
	//Calcolo la media dei segnali
	double mx=0,my=0,sx,sy,denom,sxy,r,tr;
	for(int i=0;i<x.length;i++){
	    mx+=x[i];
	    my+=y[i];
	   			     }
	int n=x.length;
	mx=mx/n;
	my=my/n;
	
	/* Calculate the denominator */
	sx = 0;
	sy = 0;
	for (int i=0;i<n;i++) {
	    sx += (x[i] - mx) * (x[i] - mx);
	    sy += (y[i] - my) * (y[i] - my);
	}
	denom = Math.sqrt(sx*sy);
	/* Calculate the correlation series */
	int maxdelay=n/2;
	mr=0.;
	for (int delay=-maxdelay;delay<maxdelay;delay++) {
	    sxy = 0;
	    for (int i=0;i<n;i++) {
		int j = i + delay;
		while (j < 0)
		    j += n;
		j %= n;
		sxy += (x[i] - mx) * (y[j] - my);
	    }
	    r = sxy / denom;
	    if(r>mr)
		{
		    mr=r;
		    mdelay=delay;
		}
	}
	
	if(mdelay==-100)
	    {
		//IJ.log("ATTENZIONE: delay non assegnato");
		mdelay=0;
	    }
	//if(mdelay==0) IJ.log("****0 DELAY; mr="+mr);
	return mdelay;
    }


	boolean showDialog(ImagePlus imp) {
	    suppress=false;
	    centre=false;	
	    Calibration cal = imp.getCalibration();
	    String units = cal.getUnits();
	    if (cal.pixelWidth==0.0)
		cal.pixelWidth = 1.0;
	    double outputSpacing = cal.pixelDepth;
	    GenericDialog gd2 = new GenericDialog("Radial Reslice");
	    
	    gd2.addNumericField("Incremento angolare in gradi (iag)", 1, 0);
	    gd2.addNumericField("Finestra per il filtro mediano (espressa in iag)",10,0);
	    gd2.addNumericField("Dl+",10,2);
	    gd2.addNumericField("Dl-",10,2);
	    gd2.addNumericField("Angolo della CC (gradi)", 0, 0);
	    gd2.addNumericField("Apertura angolare della CC (gradi)", 0, 0);
	    
	    gd2.showDialog();
	    if (gd2.wasCanceled())
		return false;
	   
	    //if (arcangle >360 || arcangle <1) arcangle = 360;
	    if (cal.pixelDepth==0.0) cal.pixelDepth = 1.0;
	    nprofiles = new Double(360 / gd2.getNextNumber()).intValue();
	    mediaRange=(int)gd2.getNextNumber();
	    outerOscillationLimit=(double)gd2.getNextNumber();
	    innerOscillationLimit=(double)gd2.getNextNumber();
	    alphaCC=(double)gd2.getNextNumber();
	    deltaAlpha=(double)gd2.getNextNumber();

	    //Istanze degli oggetti parametrici
	    myang=new double[nprofiles];
	    x=new double[mstackSize][nprofiles];
	    y=new double[mstackSize][nprofiles];
	    delay=new int[mstackSize][nprofiles];
	    rrd=new float[nprofiles/2];
	    frrd=new float[nprofiles/2];
	    prrd=new float[nprofiles/2*mstackSize];
	    filtDelay=new int[mstackSize][nprofiles];
	    fdelay=new float[mstackSize*nprofiles];
	    
	    avdelay=new double[mstackSize][nprofiles];
	    cave=new double[mstackSize][nprofiles];
	    ns=new double[mstackSize][nprofiles];
	    sd=new double[mstackSize][nprofiles];
	    l=new int[udrs][nprofiles];
	    M=new float[mstackSize];
	    
	    return true;
	}
    
	int[] getRoiCenter(Roi roi){
		int xc=0,yc=0;
		Polygon proi=roi.getPolygon();
		for(int i=0;i<proi.npoints;i++)
		{
			xc=xc+proi.xpoints[i];
			yc=yc+proi.ypoints[i];
		}
		xc=xc/proi.npoints;
		yc=yc/proi.npoints;
		int ret[]={xc,yc};
		return ret;
	}
    
	//ImagePlus
	void radslice(Roi[] croi) {
		 

		 double r = 0.0;	//The length of the line
		 double a = 0.0;	//angle increment
		 double a0 = 0.0;	//initial angle
		 double ang = 0.0;	//current angle
		 noRoi = false;
		 
		 int caz[]=getRoiCenter(croi[currentRoi]);
		 xrc[currentRoi]=caz[0];yrc[currentRoi]=caz[1];

		 
		 Line line = new Line(xrc[currentRoi], yrc[currentRoi], xrc[currentRoi]+250, yrc[currentRoi]);
		 r = line.getRawLength();

		 a = (Math.PI/180.0)*(360.0/nprofiles);
		 
		
		 //IJ.log("X2-x0="+(X2-X0)+" Y2-Y0="+(Y2-Y0)+"STARTING ANGLE:"+elAngle);
		 if (nprofiles==0) {
				IJ.error("Radial reslice", "Output Z spacing ("+IJ.d2s(outputZSpacing,0)+" pixels) is too large.");
				return;
		 }
		
		 
		 IJ.resetEscape();
		 for (int i=0; i<nprofiles; i++)	{
				ang = 0+a*(i+1);
				
				    //double th=ang;
				    //double ax=aAx*1.2;
				    //double by=bAx*1.2;
				    //double ax=r*1.2;
				    //double by=r*1.2;
				   
				if (ang < 0) ang += 2*Math.PI;
				myang[i]=ang;
				
				if (IJ.escapePressed())
					{IJ.beep();  return;}
				//Identifica i punti di intersezione tra il ragio R (quello di angolo ang) e la mainROI
			
			    //Creo un array con i punti del raggio R...
			    int mr=(int)r;
			    double[] rx=new double[mr];
			    double[] ry=new double[mr];
			   // IJ.log("RAD Slice: assegnato x e y per "+udrr[currentRoi]);
			    for(int mi=0;mi<mr;mi++)
				{
				    rx[mi]=(xrc[currentRoi]+mi*Math.cos(ang));
				    ry[mi]=(yrc[currentRoi]+mi*Math.sin(ang));
				    if(!croi[currentRoi].contains((int)rx[mi],(int)ry[mi]))
				       {
					   x[udrr[currentRoi]][i]=rx[mi];
					   y[udrr[currentRoi]][i]=ry[mi];
					   l[currentRoi][i]=mi;
					   
					   break;
				       }
				}
			    if(x[udrr[currentRoi]][i]==0)IJ.log("merda!");
			
		 }
		 //return new ImagePlus("Reslice of "+imp.getShortTitle(), stack2);
	}
    
      

	void drawLine(double x1, double y1, double x2, double y2, ImagePlus imp) {
		 ImageCanvas ic = imp.getCanvas();
		 if (ic==null) return;
		 Graphics g = ic.getGraphics();
		 g.setColor(Color.blue);
		 g.setXORMode(Color.black);
		 g.drawLine(ic.screenX((int)(x1+0.5)), ic.screenY((int)(y1+0.5)), ic.screenX((int)(x2+0.5)), ic.screenY((int)(y2+0.5)));
	}
	
	/*
	 * Calcola la nmedia del valore assoluto del campione
	 */
	public static double[] absMean(double[] m) {
		double ret[]=new double [2];
	    double mean = 0;
	    double var=0;
	    double std=0;
	    for (int i = 0; i < m.length; i++) {
	        mean += Math.abs( m[i]) ;
	    }
	    mean= mean / m.length;
	    for (int i = 0; i < m.length; i++) {
	        var +=  (Math.abs( m[i])-mean)*(Math.abs( m[i])-mean) ;
	    }
	    std=Math.sqrt(var/m.length);
	    ret[0]=mean;
	    ret[1]=std;
	    return ret;
	}
    

}
