package ojovoz.agroecoresearch;

import java.util.ArrayList;

/**
 * Created by Eugenio on 31/03/2017.
 */
public class oActivity {
    public int activityId;
    public String activityName;
    public String activityCategory;
    public int activityPeriodicity;
    public String activityMeasurementUnits;
    public String activityDescription;
    public ArrayList<oCrop> activityAppliesToCrops;
    public ArrayList<oTreatment> activityAppliesToTreatments;

    oActivity(){
        activityAppliesToCrops = new ArrayList<>();
        activityAppliesToTreatments = new ArrayList<>();
    }
}
