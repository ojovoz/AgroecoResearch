package ojovoz.agroecoresearch;

import java.util.ArrayList;

/**
 * Created by Eugenio on 31/03/2017.
 */
public class oMeasurement {
    public int measurementId;
    public String measurementName;
    public String measurementCategory;
    public String measurementSubCategory;
    public int measurementType;
    public String measurementCategories;
    public float measurementMin;
    public float measurementMax;
    public String measurementUnits;
    public int measurementPeriodicity;
    public boolean measurementHasSampleNumber;
    public boolean measurementIsCommon;
    public String measurementDescription;
    public ArrayList<oCrop> measurementAppliesToCrops;
    public ArrayList<oTreatment> measurementAppliesToTreatments;

    oMeasurement(){
        measurementAppliesToCrops = new ArrayList<>();
        measurementAppliesToTreatments = new ArrayList<>();
    }
}
