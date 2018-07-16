


import java.net.*;
import java.io.*;
import ij.*;
import ij.process.*;
import ij.gui.*;
import ij.measure.*;
import ij.plugin.frame.RoiManager;
import java.awt.*;
import java.awt.image.BufferedImage;
import java.awt.event.*;


import ij.plugin.filter.*;
import ij.measure.ResultsTable;



/* 	Author: Francesco sisini 
	Contact: Francesco Sisini ssf [at] unife.it
	Licence: Public Domain	*/
	

/**
   WARNING: this plugin is not thread safe!!!
*/


public class TM_RTJVP01 implements PlugInFilter , KeyListener,ImageListener,MouseListener{

   
   
  
    boolean kl=false;//Used to flak keylistner setted
    
    boolean fstr=true;
    float pdt=0;
    private static final double rho = 1000;
    private double outputZSpacing = 1.0;
    private int nprofiles = 1;
    int width=300;
    private ImagePlus imp;
    Overlay ovl; 
    RoiManager manager;	
    ResultsTable rt=ResultsTable.getResultsTable();
    private  double  xrc,yrc;
    private double[] myang;
    
    int currentRoi=0;  
    private int mediaRange=10;
    double outerOscillationLimit=90; 
    
    int mstackSize=10000;
    int tho=10,gtho=30;
  
    Plot plot;
    PlotWindow pw;
    
    /*Campionamenti per secondo*/
    double fps=20;
    double f_fps=1./fps;
    private double t_scale_max=10;
    
    /*Campionamento di un ciclo di JVP e ECG*/
    double[] jvp_cicle; //cm^2
    double[] ecg_cicle;
    double[] t_jvp_cicle;
    double[] t_ecg_cicle;
    
    /*Set di coordinate per il plot del tracciato JVP*/
    int selInx=0;
    int MAXSEL=(int)(fps*t_scale_max);
    float []xp;
    float []yp;
    int []ixp;
    int []iyp;
    int xLimiInf=0;
    int xLimiSup=0;
    
    /*Opzioni dialogo*/
    public boolean isRealTime=true;
    public double pixelXcm=130;
    boolean useCov=true,useGL=true,useGGL=true;
    
    private int T_cicle;
    private double ijv_length;
    private double compliance_xul;
    private double csa_x;
    
    boolean record=false;
    boolean exit=false;
    
    
    ImageWindow win;
    ImageCanvas canvas;

   
    

public void mousePressed(MouseEvent e) {
		int x = e.getX();
		int y = e.getY();
		int offscreenX = canvas.offScreenX(x);
		int offscreenY = canvas.offScreenY(y);
		showDialog(imp);
		IJ.log("Mouse pressed: "+offscreenX+","+offscreenY+modifiers(e.getModifiers()));
		//IJ.log("Right button: "+((e.getModifiers()&Event.META_MASK)!=0));
	}

	public void mouseReleased(MouseEvent e) {
		IJ.log("mouseReleased: ");
	}
	
	public void mouseDragged(MouseEvent e) {
		int x = e.getX();
		int y = e.getY();
		int offscreenX = canvas.offScreenX(x);
		int offscreenY = canvas.offScreenY(y);
		IJ.log("Mouse dragged: "+offscreenX+","+offscreenY+modifiers(e.getModifiers()));
	}

	public static String modifiers(int flags) {
		String s = " [ ";
		if (flags == 0) return "";
		if ((flags & Event.SHIFT_MASK) != 0) s += "Shift ";
		if ((flags & Event.CTRL_MASK) != 0) s += "Control ";
		if ((flags & Event.META_MASK) != 0) s += "Meta (right button) ";
		if ((flags & Event.ALT_MASK) != 0) s += "Alt ";
		s += "]";
		if (s.equals(" [ ]"))
 			s = " [no modifiers]";
		return s;
	}

	public void mouseExited(MouseEvent e) {}
	public void mouseClicked(MouseEvent e) {}	
	public void mouseEntered(MouseEvent e) {}
	public void mouseMoved(MouseEvent e) {}

    
    public void keyPressed(KeyEvent e) {
        int keyCode = e.getKeyCode();
        char keyChar = e.getKeyChar();
        int flags = e.getModifiers();
        e.consume(); 
        /*
         * Uscita
         */
        if(keyCode==27) exit=true;
        
        /*
         * Record on/off
         */
        if(keyCode==82)record=!record;//R
        
        /*
         * Controllo centro ROI di acquisizione
         * */
        if(keyCode==37) xrc--;
        if(keyCode==39) xrc++;
        if(keyCode==38) yrc--;
        if(keyCode==40) yrc++;
        
        /*
         * Controllo soglia GL
         */
        if(keyCode==90) tho--; //Z
        if(keyCode==88) tho++; //X
        /*
         * Controllo lunghezza raggio
         */
        if(keyCode==65) outerOscillationLimit++; //A
        if(keyCode==83) outerOscillationLimit--; //S
	if(keyCode==85) //U
	{
		try{
			String urlString = "http://localhost/jvp/controller.php?action=imagej&datax=";
			//Add jvp data
			String datax="";
			String datay="";
			for(int i=0;i<MAXSEL-1;i++)
			{			
			    datax=datax+xp[i]+";";
			    datay=datay+yp[i]+";";
			}
			URL url = new URL(urlString+datax+"&datay="+datay);
			IJ.log(String.valueOf(url.toString()));
			URLConnection conn = url.openConnection();
			InputStream is = conn.getInputStream();
			int i;
			char c;
			String st="";
			while((i = is.read())!=-1) {
			    c = (char)i;
			    st=st+c;
			    // prints character
			    
			}
			IJ.log(st);
			
		}catch(Exception ex)
		{
			IJ.log(ex.toString());
		}	
	}	
	


        //IJ.log("keyPressed: keyCode=" + keyCode + " (" + KeyEvent.getKeyText(keyCode) + ")");
        //IJ.log("Parameter xrc="+xrc+"; yrc="+yrc+"; GL="+tho+"; R="+outerOscillationLimit);
        
        
	//IJ.getInstance().keyPressed(e); // hand off event to ImageJ
    }
    public void imageClosed(ImagePlus imp) {
       /* if (win!=null)
            win.removeKeyListener(this);
        if (canvas!=null)
            canvas.removeKeyListener(this);
        ImagePlus.removeImageListener(this);
        IJ.log("removeKeyListener");*/
    }

    public void keyReleased(KeyEvent e) {}
    public void keyTyped(KeyEvent e) {}
    public void imageOpened(ImagePlus imp) {}
    public void imageUpdated(ImagePlus imp) {}

    public int setup(String arg, ImagePlus imp) {
	
	return DOES_ALL;
    }
    
    public void run(ImageProcessor ip) {

	ovl=new Overlay();
	imp = WindowManager.getImage("JVP");
	win = imp.getWindow();
	
    	if(win==null)
	    {
    		IJ.showMessage("Creare un'imagine vuota chiamata JVP");
    		return;
	    }
    	win.addKeyListener(this);
    	imp.setOverlay(ovl);
	canvas = win.getCanvas();
	canvas.addKeyListener(this);
	//canvas.addMouseListener(this);
	/*
	ImageWindow win = imp.getWindow();
	
	win.addKeyListener(this);
	
	ImagePlus.addImageListener(this);
	
	
	
	*/
	//canvas.addMouseMotionListener(this);
    	
        
	
        
         
         
    	
	
  
	Roi[] ra;
	manager = RoiManager.getInstance(); 
	if(manager==null)
	{
	    //IJ.showMessage("Aprire il ROIManager dal menu Analze->Tool");
	    //return;
	}else
	    {
		ra=manager.getRoisAsArray();
	    }
		
	
	int stackSize = mstackSize;
	Roi roi = imp.getRoi();
	int roiType = roi!=null?roi.getType():0;

	if (!showDialog(imp))	  return;
	if(!isRealTime)
	{
		ImageStack stack = imp.getStack();
		mstackSize = stack.getSize();
	}
	/*Parametri che devono essere configurati in base al paziente*/
	t_scale_max=10;
	T_cicle=1;
	ijv_length=20.0/100; //Distanza punto di insonificazione dall'atrio
	/*To be initiated*/
	jvp_cicle=new double[(int)fps];
	ecg_cicle=new double[(int)fps];
	t_jvp_cicle=new double[(int)fps];
	t_ecg_cicle=new double[(int)fps];

	
	/*
	 * PLOT
	 */
	
	xp = new float[MAXSEL]; 
	yp = new float[MAXSEL]; 
	ixp = new int[stackSize]; 
	iyp = new int[stackSize]; 
	
	
	PlotWindow.noGridLines = false; // draw grid lines
	
       
       long startTime = System.currentTimeMillis();	
       currentRoi=0;
       initialize();
       
       
       
       /**
	  Cross correlation between signal acquired in two differet frames 
       */
       
       
       int z=nprofiles;
       
       /**
	  STEP 1 crea l'array dei ritardi
       */
       detectJugularWall(z);
       rt.show("Results");
       plot.show();
       
       
       /**
	  STEP 3 ricalcola la ROI sui ritardi
       */
       
       IJ.showStatus(IJ.d2s(((System.currentTimeMillis()-startTime)/1000.0),2)+" seconds");
       
    }
    
    
    
    
    //Applica un filtro del valor medio all'array dei ritardi
    public double[] doAverageFilterToDelay(double []v)
    {
	
    double range= mediaRange;
	double [] d=new double[nprofiles];
		for(int j=0;j<nprofiles;j++)
		    {
			double vm=0;
			int k=0;
			for(k=-(int)range;k<range;k++)
			    {
				int ri=j+k;
				if(ri<0)ri=nprofiles+ri;
				if(ri>=nprofiles)ri=ri-nprofiles;
				vm=vm+v[ri];
			    }
			
			d[j]=(vm/(2.0*range));
	    }
	return d;

    }

    


   /*
    * Crea l'array dei ritardi per ogni angolo 
    */
    public void detectJugularWall( int z){
    //z:
	//mu:=indice angolo 
	//mt:=indice immagine (istante di acquisizione)
   
    
    int mt=0;
    int li=0;
    double st=System.currentTimeMillis();
    double ct=System.currentTimeMillis();
    boolean nxt=true;
    while(nxt)
    {
    	if(!isRealTime && mt>(mstackSize-1)) nxt=false;
	//plotOverImage(ixp, iyp);
	if (IJ.escapePressed()||exit==true)
	    {IJ.beep();  return;}
    	
    	if(System.currentTimeMillis()-ct>(T_cicle*1000) || (li>=fps)) 
		{
    		calculateModelParametrs();
    		li=0;
    		ct=System.currentTimeMillis();
    		jvp_cicle=new double[(int)fps];
    		ecg_cicle=new double[(int)fps];
    		t_jvp_cicle=new double[(int)fps];
    		t_ecg_cicle=new double[(int)fps];

		}

	/*
	  reset m if more than t_scale_max has passed
	 */
    	if((System.currentTimeMillis()-st>(t_scale_max*1000))&&isRealTime) 
    		{
    		st=System.currentTimeMillis();
    		mt=0;
    		
    		}
    	
    	mt++;
    	
    	Rectangle screenRect = new Rectangle(0,0,500,500);
       if(isRealTime)
       {
    	try{
        	BufferedImage capture = new Robot().createScreenCapture(screenRect);
        	imp.setHideOverlay(false);
        	imp.setImage(capture);
		//Added 16 07 2018
		if(!kl)
		    {
			kl=true;
			win = imp.getWindow();
			win.addKeyListener(this);
			canvas = win.getCanvas();
			canvas.addKeyListener(this);
			canvas.removeKeyListener(IJ.getInstance());
			//canvas.addMouseListener(this);
		    }
        }
        catch(Exception ex)
        {
        	IJ.log("CAZ...");
        }
       }else
       {
    	   imp.setSlice(mt+1);
       }
    	
    	double distance[]=new double[z];
    	currentRoi=0;
    	//Ciclo su 2PI
    	for(int mu=1;mu<=z;mu++)
    	{
    		/* Calcolo il raggio ellittico */
    		double hth=myang[mu-1];
    		int xf=(int)Math.round( xrc+(outerOscillationLimit)*(Math.cos(hth)));
    		int yf=(int)Math.round( yrc+(outerOscillationLimit)*(Math.sin(hth)));
    		
    		int xi=(int)xrc;
    		int yi=(int)yrc;
    		Line ln=new Line(xi,yi,xf,yf);
    		//drawLine(xi, yi, xf, yf, imp);
    
			imp.setSlice(mt);
			imp.setRoi(ln,true);		
			double[] s1=ln.getPixels();
			int mdel=0;
			
			//mdel+=findMax(s1, s2);
			int kk=0;
			if(useGL==true)
			{
				mdel+=gl_treshold(s1, tho);
				kk++;
			}
			if(useGGL==true)
			{
				mdel+=ggl_treshold(s1, gtho);
				kk++;
			}
			
			distance[mu-1]=(double)mdel/(double)kk;
	    }
    	/*
    	 * Post processing distanze raggi di controllo
    	 */
    	distance=doAverageFilterToDelay(distance);
		/*
		 * Definizione della ROI
		 */
    	int[] xr=new int[nprofiles];
		int[] yr=new int[nprofiles];
    	for(int mu=1;mu<=z;mu++)
    	{
	    double hth=myang[mu-1];
	    xr[mu-1]=(int)Math.round( xrc+(distance[mu-1])*(Math.cos(hth)));
	    yr[mu-1]=(int)Math.round( yrc+(distance[mu-1])*(Math.sin(hth)));
	}
	PolygonRoi myROI= new PolygonRoi(xr, yr,xr.length, Roi.POLYGON);
	imp.setRoi(myROI);
    	Analyzer a=new Analyzer(imp);
    	ImageStatistics ist=imp.getStatistics();
    	
	imp.setSliceWithoutUpdate(mt+1);
	double area=ist.area/(pixelXcm*pixelXcm)*(1.0/(100*100));//area in m^2
	double stime=(System.currentTimeMillis()-st)/1000.;
	/*Aggiorna i campioni ECG e JVP*/
	jvp_cicle[li]=area;
	t_jvp_cicle[li]=(System.currentTimeMillis()-ct)/1000.;
	//Sostituire con un ECG detection ALG
	ecg_cicle[li]=area;
	t_ecg_cicle[li]=(System.currentTimeMillis()-ct-180)/1000.;
	
	double pressure=(area-csa_x)*(1./compliance_xul)*(1.0/133.3);
	
	if(record)
	    {
		if(fstr)
		    {
			xp = new float[MAXSEL]; 
			yp = new float[MAXSEL];
			fstr=false;
			if(pw!=null)
			    pw.close();
			plot = new Plot("JVP","Time (s)","Units");
			plot.setLimits(xLimiInf, t_scale_max, -3, 5);
			plot.setLineWidth(3);
			plot.setColor(Color.blue);
			plot.changeFont(new Font("Helvetica", Font.PLAIN, 24));
			plot.addLabel(0.15, 0.95, "JVP");
			plot.changeFont(new Font("Helvetica", Font.PLAIN, 16));
			plot.setColor(Color.blue);
			pw=plot.show();
		    }
		
    		//manager.add(imp,myROI,mt);
    		//a.saveResults(ist,myROI);
		xp[selInx]=(float)stime;
		yp[selInx]=(float)pressure;
		plot.addPoints(xp, yp,PlotWindow.LINE);
		plot.addPoints(xp, yp,PlotWindow.X);
		
		if((selInx<MAXSEL-1)&&stime<9.5)
		    selInx++;
		else
		    {
			fstr=true;
			selInx=0;
			xp = new float[MAXSEL]; 
			yp = new float[MAXSEL];
		    }
	    }else
	    {
		//plot= new Plot("JVP","Time (s)","Units");
		selInx=0;
		fstr=true;
		 
	    }
	
	//plotOverImage(ixp, iyp);
	
	//manager.select(0);
	IJ.wait((int)fps);
	li++;
    }
    }
    
    private void calculateModelParametrs() {
		// TODO Auto-generated method stub
    	
    	//identifica gli istanti relativi alle onde "a" ed "R"
    	int i_ecg=findMaxIndex(ecg_cicle);
    	int i_jvp=findMaxIndex(jvp_cicle);
    	csa_x = findMin(jvp_cicle);
    	//csa_x=0.5/10000;
    	double dt=(t_jvp_cicle[i_jvp]-t_ecg_cicle[i_ecg]);
    	if(dt<0)
    	{
    		IJ.showMessage("L'intervallo a-R risulta negativo: "+dt);
    	}
    	//Calcola la velocità dell'onda di pressione
    	double c=ijv_length/dt;//c deve essere in m/s
    	//Calcola la compliance
    	compliance_xul=csa_x/(rho*c*c);
    	//IJ.log("dt="+dt+"; csa_x="+csa_x+" C'="+compliance_xul+"; i_jvp="+i_jvp);
		
	}


	private double findMin(double[] x) {
		double mx= 1000;
    	for(int i=0;i<x.length;i++)
    	{
    		if(x[i] <mx&&x[i]>0){
    			mx=x[i];
    		}
    	}
    	return mx;
	}


	private void plotOverImage(int []x,int []y)
    {
    	 ImageCanvas ic = imp.getCanvas();
		 if (ic==null) return;
		 Graphics g = ic.getGraphics();
		 
		 g.setColor(Color.red);
		 g.drawPolygon (x, y, x.length);
		 //ovl.add(new PolygonRoi(x, y, x.length, imp, 0));
		 //imp.setOverlay(ovl);
    }
  
    /*
     * Calcola la nuova roi
     */
    
    /**
     * Questo metodo assume che il bordo della IJV abbia un gradiente del livello di grigio (GGL) superiore ad un dato valore 
     * di soglia.
     * Si analizza il GGL dei pixel lungo un raggio della sezione della IJV valutato su due diversi frame (quindi a tempi diversi)
     * e si registra l'indice del primo pixel che dal centro supera la soglia. La differenza degli indici tra i due frame 
     * è assunta come lo spostamento del bordo della IJV tra gli istanti di tempo relativi ai due frame.   
     * @param x Array dei pixel lungo un dato raggio valutati sul primo frame
     * @param y Array dei pixel lungo un dato raggio valutati sul  frame in considerazione
     * @param th Valore di soglia
     * @return
     */
   
    int ggl_treshold(double[] x,int th)
    {
    	int ix=0,iy=0;
    	
    	boolean xok=false,yok=false;
    	//Calcolo il gradiente
    	double[] gx=new double[x.length];
    	
    	for(int i=0;i<x.length-1;i++)
    	{
    		gx[i]=x[i+1]-x[i];
    	}
    	
    	
    	for(int i=0;i<x.length;i++)
    	{
    		if(Math.abs(gx[i]) >th && !xok){
    			ix=i;
    			
    			xok=true;
    		}
    		
    	}
    	return ix;
    	
    }
    /**
     * Questo metodo assume che il bordo della IJV abbia un livello di grigio (GL) superiore ad un valore di soglia.
     * Si analizza il GL dei pixel lungo un raggio della sezione della IJV valutato su due diversi frame (quindi a tempi diversi)
     * e si registra l'indice del primo pixel che dal centro supera la soglia. La differenza degli indici tra i due frame 
     * è assunta come lo spostamento del bordo della IJV tra gli istanti di tempo relativi ai due frame.   
     * @param x Array dei pixel lungo un dato raggio valutati sul primo frame
     * @param y Array dei pixel lungo un dato raggio valutati sul  frame in considerazione
     * @param th Valore di soglia
     * @return
     */
    int gl_treshold(double[] x, int th)
    {
    	int ix=0;
    	boolean xok=false,yok=false;
    	for(int i=0;i<x.length;i++)
    	{
    		if(Math.abs(x[i]) >th && !xok){
    			ix=i;
    			
    			xok=true;
    		}
    	}
    	return ix;
    	
    }
    
    int findMaxIndex(double[] x)
    {
    	double mx=0;
    	int ix=0;
    	for(int i=0;i<x.length;i++)
    	{
    		if(x[i] >mx){
    			
    			mx=x[i];
    			ix=i;
    		}
    	}
    	return ix;
    }
    double findMax(double[] x)
    {
    	double mx=0;
    	
    	for(int i=0;i<x.length;i++)
    	{
    		if(x[i] >mx){
    			
    			mx=x[i];
    			
    		}
    	}
    	return mx;
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
	
	Calibration cal = imp.getCalibration();
	String units = cal.getUnits();
	if (cal.pixelWidth==0.0)
	    cal.pixelWidth = 1.0;
	double outputSpacing = cal.pixelDepth;
	GenericDialog gd2 = new GenericDialog("Radial Reslice");
	
	gd2.addCheckbox("Acquisizione in Real-Time", true);
	gd2.addNumericField("Pixel per cm", 130, 0);
	gd2.addNumericField("Soglia GL", 30, 0);
	gd2.addNumericField("Soglia gradiente GL", 0, 0);
	gd2.addNumericField("Incremento angolare in gradi (iag)", 1, 0);
	gd2.addNumericField("Finestra per il filtro mediano (espressa in iag)",10,0);
	gd2.addNumericField("Dl+",90,2);
	
	
	
	gd2.showDialog();
	if (gd2.wasCanceled())
	    return false;
	
	if (cal.pixelDepth==0.0) cal.pixelDepth = 1.0;
	isRealTime=gd2.getNextBoolean();
	pixelXcm=gd2.getNextNumber();
	
	tho=new Double(gd2.getNextNumber()).intValue();
	if(tho==0) useGL=false;
	gtho=new Double(gd2.getNextNumber()).intValue();
	if(gtho==0) useGGL=false;
	nprofiles = new Double(360 / gd2.getNextNumber()).intValue();
	mediaRange=(int)gd2.getNextNumber();
	outerOscillationLimit=(double)gd2.getNextNumber();
	myang=new double[nprofiles];	    
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
	void initialize() {
	    double a = 0.0;	//angle increment
	    double ang = 0.0;	//current angle
	    
	    xrc=200;yrc=200;
	    a = (Math.PI/180.0)*(360.0/nprofiles);
	    if (nprofiles==0) {
		IJ.error("Radial reslice", "Output Z spacing ("+IJ.d2s(outputZSpacing,0)+" pixels) is too large.");
		return;
	    }
	    IJ.resetEscape();
	    for (int i=0; i<nprofiles; i++)	{
		ang = 0+a*(i+1);
		if (ang < 0) ang += 2*Math.PI;
		myang[i]=ang;
		/*if (IJ.escapePressed())
		  {IJ.beep();  return;}*/
		
	    }
	    
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
