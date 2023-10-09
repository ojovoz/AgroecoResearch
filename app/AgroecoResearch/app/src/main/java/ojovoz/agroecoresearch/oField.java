package ojovoz.agroecoresearch;

import java.util.ArrayList;
import java.util.Date;

/**
 * Created by Eugenio on 31/03/2017.
 */
public class oField {
    public int fieldId;
    public String fieldName;
    public int fieldReplicationN;
    public Date fieldCreated;
    public String fieldLat;
    public String fieldLng;
    public int nCrops;
    public boolean hasIntercropping;
    public boolean hasSoilManagement;
    public boolean hasPestControl;
    public int rows;
    public int columns;
    public ArrayList<oPlot> plots;
    public int parentFieldId;

    oField(int id, String name, int rn, Date created, String lat, String lng, int nc, boolean i, boolean s, boolean p, int r, int c, ArrayList<oPlot> pp, int pf){
        fieldId=id;
        fieldName=name;
        fieldReplicationN=rn;
        fieldCreated=created;
        fieldLat=lat;
        fieldLng=lng;
        nCrops=nc;
        hasIntercropping=i;
        hasSoilManagement=s;
        hasPestControl=p;
        rows=r;
        columns=c;
        plots=pp;
        pf=parentFieldId;
    }

    oField(){

    }
}
