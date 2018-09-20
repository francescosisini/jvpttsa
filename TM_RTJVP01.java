


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

    /* Coordinate finestra JVP e Plot */
    int wjx,wjy,wjw,wjh;
    int wpx,wpy,wpw,wph;
    int wrx,wry,wrw,wrh;
    

    /* stato applicativo*/
    boolean paused=false;
    boolean fstr=true;
    boolean kl=false;//Used to flak keylistner setted
    boolean ecgrecording=false;
    boolean record=false;
    boolean exit=false;
    
    String HOST="http://daa.tekamed.it/jvp";
    String repository="TEKAMED";
    
    int Jpos=2;
    int videon=1;
    String LoR="";
    
    String PID="John Doe";
    float pdt=0;
    static final double rho = 1000;
    double outputZSpacing = 1.0;
    int nprofiles = 1;
    int width=300;
    ImagePlus imp;
    ImageProcessor ipr;
    Overlay ovl; 
    RoiManager manager;	
    ResultsTable rt=ResultsTable.getResultsTable();
    double  xrc,yrc;//coordinate del centro di scansione delle pareti giugulari
    int  xecg=400,yecg=400;//coordinate dell'angolo superiore sinistro del rettangolo di ricerca del ECG
    
    double[] myang;
    
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
    float []xp; // La base dei tempi
    float []yp; // La CSA
    float []ep; // La traccia ECG
    int []ixp;
    int []iyp;
    int xLimiInf=0;
    int xLimiSup=0;
    
    /*Opzioni dialogo*/
    public boolean isRealTime=true;
    public double pixelXcm=0;
    boolean useCov=true,useGL=true,useGGL=true;
    
    private int T_cicle;
    private double ijv_length;
    private double compliance_xul;
    private double csa_x;

    
    
    
    ImageWindow win;
    ImageCanvas canvas;


    public float getECGValue()
    {
        int[] vc=new int[3];
        //int w=150;
        //int h=150;
        int h=wjh-yecg;
        for (int y=yecg; y<wjh; y++) {
            for (int x=xecg; x<wjw; x++) {
                //ipr.getPixel (wjw-x,wjh-h+y,vc);
                ipr.getPixel (x,y,vc);
                if(vc[0]>1.4*vc[1] && vc[0]>vc[2]*1.4 && vc[0]>150)
                    {
                        //IJ.log("ECG "+y);
                        return (wjh-y)/(float)h*(float)3.;
                    }
            }
        }
        IJ.log("R:"+vc[0]+" G:"+vc[1]+" B:"+vc[2]);
        return -1;
        
    }
    
    
    
    public void mousePressed(MouseEvent e) {
        
        int x = e.getX();
        int y = e.getY();
        if(ecgrecording)
            {
                xecg=x;
                yecg=y;
            }
        else
            {
                xrc=x;
                yrc=y;
            }
    }
    
    public void mouseReleased(MouseEvent e) {
        
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
         * ECG on/off
         */
        if(keyCode==69)ecgrecording=!ecgrecording;//E

        
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
        if(keyCode==83) outerOscillationLimit++; //A
	if(keyCode==80){
	    paused=!paused; //P Pause the Robot
	}
        if(keyCode==65) outerOscillationLimit--; //S
	if(keyCode==84) //T
	    showSettingsDialog(imp);

	if(keyCode==85) //U
	{
	    /*GET Patiant data before upload */
	    if(!showUploadDialog (imp)) return;
	    
		try{
		    String PID2=URLEncoder.encode(PID, "UTF-8");
		    
		    String urlString = HOST+"/controller.php?action=imagej";
		    urlString=urlString+"&repository="+repository+"&videon="+videon+"&jpos="+Jpos+"&lor="+LoR+"&PID="+PID2+"&datax=";
		    //Add jvp data
		    String datax="";
		    String datay="";
                    String datae="";
		    for(int i=0;i<MAXSEL-1;i++)
			{			
                            if(xp[i]>0)
                                {
                                    datax=datax+xp[i]+";";
                                    datay=datay+yp[i]+";";
                                    datae=datae+ep[i]+";";
                                }
                        }
                    if(ecgrecording)
                        {
                            urlString=urlString+datax+"&datay="+datay+"&ecg=1&datae="+datae;
                        }else
                        {
                            urlString=urlString+datax+"&datay="+datay+"&ecg=0";
                        }
                    URL url = new URL(urlString);
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
    }
    public void imageClosed(ImagePlus imp) {
        

    }

    public void keyReleased(KeyEvent e) {}
    public void keyTyped(KeyEvent e) {}
    public void imageOpened(ImagePlus imp) {}
    public void imageUpdated(ImagePlus imp) {}

    public int setup(String arg, ImagePlus imp) {
	
	return DOES_ALL;
    }

    void setWindosLocation()
    {
      
        
        
        wjx=IJ.getInstance().getX();
        wjy=IJ.getInstance().getY()+120;
       
        
        wjw=580;
        wjh=500;
        wpx=wjx;
        wpy=wjy+wjh+50;
        wpw=wjw;
        wph=250;
        wrx=0;
        wry=0;
        wrw=wjw;
        wrh=wjh;
    }
    
    public void run(ImageProcessor ip) {
        ipr=ip;
        setWindosLocation();
	ovl=new Overlay();
	imp = WindowManager.getImage("JVP");
	win = imp.getWindow();
	win.setLocationAndSize(wjx,wjy,wjw,wjh);
    	if(win==null)
	    {
    		IJ.showMessage("Creare un'imagine vuota chiamata JVP");
    		return;
	    }
    	win.addKeyListener(this);
    	imp.setOverlay(ovl);
	canvas = win.getCanvas();
	canvas.addKeyListener(this);
	canvas.addMouseListener(this);
	    		
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
        ep = new float[MAXSEL]; 
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
        
        
        
       /**
	  STEP 3 ricalcola la ROI sui ritardi
       */
       
       //IJ.showStatus(IJ.d2s(((System.currentTimeMillis()-startTime)/1000.0),2)+" seconds");
       
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

		if(!paused){
		/*
		  reset m if more than t_scale_max has passed
		*/
		if((System.currentTimeMillis()-st>(t_scale_max*1000))&&isRealTime) 
		    {
			st=System.currentTimeMillis();
			mt=0;
			
		    }
		
		mt++;
		
		Rectangle screenRect = new Rectangle(wrx,wry,wrw,wrh);
		if(isRealTime&&!paused)
		    {
			try{
			    BufferedImage capture = new Robot().createScreenCapture(screenRect);
			    imp.setHideOverlay(false);
			    imp.setImage(capture);
                            ipr=imp.getProcessor();
			    //Added 16 07 2018
			    if(!kl)
				{
				    kl=true;
				    win = imp.getWindow();
				    win.addKeyListener(this);
				    canvas = win.getCanvas();
				    canvas.addKeyListener(this);
				    canvas.removeKeyListener(IJ.getInstance());
				    canvas.addMouseListener(this);
				}
			}
			catch(Exception ex)
			    {
				IJ.log("CAZ...");
			    }
		    }else
		    {
			// * imp.setSlice(mt+1);
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
			
			// * imp.setSlice(mt);
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
		
		// * imp.setSliceWithoutUpdate(mt+1);
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
				plot.setLimits(xLimiInf, t_scale_max, 0, 2);
				plot.setLineWidth(2);
				
				plot.changeFont(new Font("Helvetica", Font.PLAIN, 24));
				plot.addLabel(0.15, 0.95, "JVP");
				plot.changeFont(new Font("Helvetica", Font.PLAIN, 16));
				
				pw=plot.show();
                                pw.setLocationAndSize(wpx,wpy,wpw,wph);
                                plot.setSize(wpw, wph);
                                imp.getWindow().setFocusable(true);
                                imp.getWindow().requestFocus();
                            }
			
			
			xp[selInx]=(float)stime;
			yp[selInx]=(float)area*(10000);//cm^2
                        ep[selInx]=(float)(getECGValue());
                        plot.setColor(Color.blue);
                        plot.addPoints(xp, yp,PlotWindow.LINE);
                        //plot.setColor(Color.black);
			//plot.addPoints(xp, yp,PlotWindow.X);
                        if(ecgrecording)
                            {
                                plot.setColor(Color.red);
                                plot.addPoints(xp, ep,PlotWindow.LINE);
                            }
                        imp.getWindow().requestFocus();
                        if((selInx<MAXSEL-1)&&stime<9.5)
			    selInx++;
			else
			    {
				fstr=true;
				selInx=0;
				xp = new float[MAXSEL]; 
				yp = new float[MAXSEL];
                                ep = new float[MAXSEL];
			    }
		    }else
		    {
                        selInx=0;
			fstr=true;
                    }
		IJ.wait((int)fps);
		li++;
                }
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


boolean showSettingsDialog(ImagePlus imp)
    {
	GenericDialog gd2 = new GenericDialog("Set parameters");
	gd2.addStringField("Web repository",repository);
	gd2.addStringField("Patient or Study ID",PID);
	gd2.addNumericField("Pixel per cm", pixelXcm, 0);
	gd2.addNumericField("J (1, or 3)", Jpos, 0);
	gd2.addNumericField("Video number", videon, 0);
	gd2.addStringField("L/R",LoR);

	gd2.showDialog();
	if (gd2.wasCanceled())
	    {
		return false;
	    }
	repository=gd2.getNextString();
	PID=gd2.getNextString();
	pixelXcm=(int)gd2.getNextNumber();
	Jpos=(int)gd2.getNextNumber();
	videon=(int)gd2.getNextNumber();
	LoR=gd2.getNextString();
	return true;
	
    }
    
    boolean showUploadDialog(ImagePlus imp)
    {
	GenericDialog gd2 = new GenericDialog("Upload confirm");
        gd2.addStringField("Remote host",HOST);
	gd2.addStringField("Repository",repository);
	gd2.addStringField("Patient ID",PID);
	gd2.addNumericField("Pixel per cm", pixelXcm, 0);
	gd2.addNumericField("J (1, or 3)", Jpos, 0);
	gd2.addNumericField("Acquisition number", videon, 0);
	gd2.addStringField("L/R",LoR);
	
	gd2.showDialog();
	if (gd2.wasCanceled())
	    {
		return false;
	    }
        HOST=gd2.getNextString();
	repository=gd2.getNextString();
	PID=gd2.getNextString();
	pixelXcm=(int)gd2.getNextNumber();
	Jpos=(int)gd2.getNextNumber();
	videon=(int)gd2.getNextNumber();
	LoR=gd2.getNextString();
	return true;
	
    }
    
    boolean showDialog(ImagePlus imp) {
	
	Calibration cal = imp.getCalibration();
	String units = cal.getUnits();
	if (cal.pixelWidth==0.0)
	    cal.pixelWidth = 1.0;
	double outputSpacing = cal.pixelDepth;
	GenericDialog gd2 = new GenericDialog("JVP Ready!");
	
	gd2.addCheckbox("Acquisizione in Real-Time", true);
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
