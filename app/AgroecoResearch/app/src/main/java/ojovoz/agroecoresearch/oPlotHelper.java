package ojovoz.agroecoresearch;

/**
 * Created by Eugenio on 20/08/2017.
 */
public class oPlotHelper {

    oPlot plot;
    int plotNumber;
    boolean state;
    boolean chooseable;

    oPlotHelper(oPlot rPlot, int rPlotNumber, boolean rState, boolean rChooseable){
        plot=rPlot;
        plotNumber=rPlotNumber;
        state=rState;
        chooseable=rChooseable;
    }

    oPlotHelper(){

    }
}
