package ojovoz.agroecoresearch;

import java.util.ArrayList;

/**
 * Created by Eugenio on 31/03/2017.
 */
public class oPlot {
    public int plotNumber;
    public int row;
    public int column;
    public oCrop primaryCrop;
    public oCrop intercroppingCrop;
    public boolean hasSoilManagement;
    public boolean hasPestControl;

    oPlot(int r, int c, oCrop c1, oCrop l, boolean sm, boolean pc){
        row=r;
        column=c;
        primaryCrop=c1;
        intercroppingCrop=l;
        hasSoilManagement=sm;
        hasPestControl=pc;
    }

    oPlot(){

    }
}
