package ojovoz.agroecoresearch;

import android.content.Context;
import android.graphics.Color;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.Iterator;
import java.util.List;
import java.util.TimeZone;

import au.com.bytecode.opencsv.CSVReader;

/**
 * Created by Eugenio on 02/04/2017.
 */
public class agroecoHelper {

    private Context context;
    ArrayList<oCrop> crops;
    ArrayList<oTreatment> treatments;
    ArrayList<oTreatmentColor> treatmentColors;
    ArrayList<oField> fields;
    ArrayList<oActivity> activities;
    ArrayList<oActivityCalendar> activitiesCalendar;
    ArrayList<oMeasurement> measurements;
    ArrayList<oMeasurementCalendar> measurementsCalendar;
    ArrayList<oHealthReport> healthReportItems;

    ArrayList<oLog> log;
    ArrayList<oInputLog> inputLog;
    ArrayList<oInputLogCalendar> inputLogCalendar;

    agroecoHelper(Context context, String catalogsNeeded){
        this.context=context;
        if(catalogsNeeded.contains("crops")) {
            createCrops();
        }
        if(catalogsNeeded.contains("treatments")) {
            createTreatments();
        }
        if(catalogsNeeded.contains("fields")) {
            createFields();
        }
        if(catalogsNeeded.contains("activities")) {
            createActivities();
        }
        if(catalogsNeeded.contains("measurements")) {
            createMeasurements();
        }
        if(catalogsNeeded.contains("log")) {
            createLog();
        }
        if(catalogsNeeded.contains("input_log")) {
            createInputLog();
        }
    }

    public void createCrops(){
        crops = new ArrayList<>();
        List<String[]> cropsCSV = readCSVFile("crops");
        if(cropsCSV!=null) {
            Iterator<String[]> iterator = cropsCSV.iterator();
            while (iterator.hasNext()) {
                String[] record = iterator.next();
                oCrop crop = new oCrop();
                crop.cropId = Integer.parseInt(record[0]);
                crop.cropName = record[1];
                crop.cropSymbol = record[2];
                crop.cropVariety = record[3];
                crops.add(crop);
            }
        }
    }

    public void createActivities(){
        activities = new ArrayList<>();
        activitiesCalendar = new ArrayList<>();
        List<String[]> activitiesCSV = readCSVFile("activities");
        List<String[]> activitiesAppliedCSV = readCSVFile("activities_applied");
        String activitiesCalendarFile = readFromFile("activities_calendar");
        if(activitiesCSV!=null) {
            Iterator<String[]> iterator = activitiesCSV.iterator();
            while (iterator.hasNext()) {
                String[] record = iterator.next();
                oActivity activity = new oActivity();
                activity.activityId = Integer.parseInt(record[0]);
                activity.activityName = record[1];
                activity.activityCategory = record[2];
                activity.activityPeriodicity = Integer.parseInt(record[3]);
                activity.activityMeasurementUnits = record[4];
                activity.activityDescription = record[5];
                activities.add(activity);
            }

            if (activitiesAppliedCSV != null) {
                Iterator<String[]> iteratorApplied = activitiesAppliedCSV.iterator();
                while (iteratorApplied.hasNext()) {
                    String[] record = iteratorApplied.next();
                    addCropTreatmentToActivity(Integer.parseInt(record[0]), record[1], record[2]);
                }
            }

            if(!activitiesCalendarFile.isEmpty()){
                String[] activitiesCalendarLines = activitiesCalendarFile.split(";");
                for(int i=0;i<activitiesCalendarLines.length;i++){
                    oActivityCalendar aC = new oActivityCalendar();
                    String[] activitiesCalendarParts = activitiesCalendarLines[i].split(",");
                    aC.activityId=Integer.parseInt(activitiesCalendarParts[0]);
                    aC.plotN=Integer.parseInt(activitiesCalendarParts[1]);
                    aC.fieldId=Integer.parseInt(activitiesCalendarParts[2]);
                    aC.date=activitiesCalendarParts[3];
                    activitiesCalendar.add(aC);
                }
            }
        }
    }

    public void createMeasurements(){
        measurements = new ArrayList<>();
        measurementsCalendar = new ArrayList<>();
        List<String[]> measurementsCSV = readCSVFile("measurements");
        List<String[]> measurementsAppliedCSV = readCSVFile("measurements_applied");
        String measurementsCalendarFile = readFromFile("measurements_calendar");
        if(measurementsCSV!=null) {
            Iterator<String[]> iterator = measurementsCSV.iterator();
            while (iterator.hasNext()) {
                String[] record = iterator.next();
                oMeasurement measurement = new oMeasurement();
                measurement.measurementId = Integer.parseInt(record[0]);
                measurement.measurementName = record[1];
                measurement.measurementCategory = record[2];
                measurement.measurementSubCategory = record[3];
                measurement.measurementType = Integer.parseInt(record[4]);
                measurement.measurementMin = Float.parseFloat(record[5]);
                measurement.measurementMax = Float.parseFloat(record[6]);
                measurement.measurementUnits = record[7];
                measurement.measurementCategories = record[8];
                measurement.measurementPeriodicity = Integer.parseInt(record[9]);
                measurement.measurementHasSampleNumber = (Integer.parseInt(record[10])==0) ? false : true;
                measurement.measurementIsCommon = (Integer.parseInt(record[11])==0) ? true : false;
                measurement.measurementDescription = record[12];
                measurements.add(measurement);
            }

            if (measurementsAppliedCSV != null) {
                Iterator<String[]> iteratorApplied = measurementsAppliedCSV.iterator();
                while (iteratorApplied.hasNext()) {
                    String[] record = iteratorApplied.next();
                    addCropTreatmentToMeasurement(Integer.parseInt(record[1]), record[2], record[3]);
                }
            }

            if(!measurementsCalendarFile.isEmpty()){
                String[] measurementsCalendarLines = measurementsCalendarFile.split(";");
                for(int i=0;i<measurementsCalendarLines.length;i++){
                    oMeasurementCalendar mC = new oMeasurementCalendar();
                    String[] measurementsCalendarParts = measurementsCalendarLines[i].split(",");
                    mC.measurementId=Integer.parseInt(measurementsCalendarParts[0]);
                    mC.plotN=Integer.parseInt(measurementsCalendarParts[1]);
                    mC.fieldId=Integer.parseInt(measurementsCalendarParts[2]);
                    mC.date=measurementsCalendarParts[3];
                    measurementsCalendar.add(mC);
                }
            }
        }
    }

    public void createHealthReportItems(){
        healthReportItems = new ArrayList<>();
        List<String[]> healthReportItemsCSV = readCSVFile("health_report_items");
        if (healthReportItemsCSV != null) {
            Iterator<String[]> iteratorHealthReport = healthReportItemsCSV.iterator();
            while (iteratorHealthReport.hasNext()) {
                String[] record = iteratorHealthReport.next();
                oHealthReport healthReport = new oHealthReport();
                healthReport.itemName = record[1];
                healthReport.categories = record[2].split(",");
                healthReportItems.add(healthReport);
            }
        }
    }

    public void addCropTreatmentToActivity(int aId, String cId, String tId){
        Iterator<oActivity> iterator = activities.iterator();
        while (iterator.hasNext()) {
            oActivity activity = iterator.next();
            if(activity.activityId==aId){
                if(!cId.isEmpty()){
                    oCrop aC = getCropFromId(Integer.parseInt(cId));
                    activity.activityAppliesToCrops.add(aC);
                }
                if(!tId.isEmpty()){
                    oTreatment aT = getTreatmentFromId(Integer.parseInt(tId));
                    activity.activityAppliesToTreatments.add(aT);
                }
                break;
            }
        }
    }

    public void addCropTreatmentToMeasurement(int mId, String cId, String tId){
        Iterator<oMeasurement> iterator = measurements.iterator();
        while (iterator.hasNext()) {
            oMeasurement measurement = iterator.next();
            if(measurement.measurementId==mId){
                if(!cId.isEmpty()){
                    oCrop mC = getCropFromId(Integer.parseInt(cId));
                    measurement.measurementAppliesToCrops.add(mC);
                }
                if(!tId.isEmpty()){
                    oTreatment mT = getTreatmentFromId(Integer.parseInt(tId));
                    measurement.measurementAppliesToTreatments.add(mT);
                }
                break;
            }
        }
    }

    public void createTreatments(){
        treatments = new ArrayList<>();
        treatmentColors = new ArrayList<>();
        List<String[]> treatmentsCSV = readCSVFile("treatments");
        List<String[]> treatmentColorsCSV = readCSVFile("treatment_colors");
        if(treatmentsCSV!=null) {
            Iterator<String[]> iterator = treatmentsCSV.iterator();
            while (iterator.hasNext()) {
                String[] record = iterator.next();
                oTreatment treatment = new oTreatment();
                treatment.treatmentId = Integer.parseInt(record[0]);
                treatment.treatmentName = record[1];
                treatment.treatmentCategory = record[2];
                if (!record[3].isEmpty()) {
                    oCrop treatmentPrimaryCrop = getCropFromId(Integer.parseInt(record[3]));
                    treatment.primaryCrop = treatmentPrimaryCrop;
                } else {
                    treatment.primaryCrop = null;
                }
                if (!record[4].isEmpty()) {
                    oCrop treatmentIntercroppingCrop = getCropFromId(Integer.parseInt(record[4]));
                    treatment.intercroppingCrop = treatmentIntercroppingCrop;
                } else {
                    treatment.intercroppingCrop = null;
                }
                treatments.add(treatment);
            }
            if(treatmentColorsCSV!=null){
                Iterator<String[]> iteratorColors = treatmentColorsCSV.iterator();
                while (iteratorColors.hasNext()) {
                    String[] record = iteratorColors.next();
                    oTreatmentColor treatmentColor = new oTreatmentColor();
                    treatmentColor.treatmentCode=Integer.parseInt(record[0]);;
                    treatmentColor.colorCode="#"+record[1];
                    treatmentColors.add(treatmentColor);
                }
            }
        }
    }

    public int getTreatmentColor(int treatmentCode){
        int ret = Color.parseColor("#CCCCCC");
        oTreatmentColor tc;
        for(int i=0;i<treatmentColors.size();i++){
            tc=treatmentColors.get(i);
            if(tc.treatmentCode==treatmentCode){
                ret=Color.parseColor(tc.colorCode);
                break;
            }
        }
        return ret;
    }

    public void createLog(){
        log = new ArrayList<>();
        String logString = readFromFile("log");
        if(!logString.isEmpty()) {
            String[] logItems = logString.split("\\|");
            for(int i=0;i<logItems.length;i++) {
                String[] logItemParts = logItems[i].split(";");
                oLog tLog = new oLog();
                tLog.logFieldId = Integer.parseInt(logItemParts[0]);
                tLog.logPlots = logItemParts[1];
                tLog.logUserId = Integer.parseInt(logItemParts[2]);
                tLog.logCropId = Integer.parseInt(logItemParts[3]);
                tLog.logTreatmentId = Integer.parseInt(logItemParts[4]);
                tLog.logMeasurementId = Integer.parseInt(logItemParts[5]);
                tLog.logActivityId = Integer.parseInt(logItemParts[6]);
                tLog.logDate = stringToDate(logItemParts[7]);
                tLog.logNumberValue = Float.parseFloat(logItemParts[8]);
                tLog.logValueUnits = logItemParts[9];
                tLog.logTextValue = logItemParts[10];
                tLog.logLaborers = logItemParts[11];
                tLog.logCost = logItemParts[12];
                tLog.logComments = logItemParts[13];
                tLog.logId = Integer.parseInt(logItemParts[14]);
                tLog.logSampleNumber = Integer.parseInt(logItemParts[15]);
                log.add(tLog);
            }
        }
    }

    public void createInputLog(){
        inputLog = new ArrayList<>();
        inputLogCalendar = new ArrayList<>();
        String inputLogString = readFromFile("input_log");
        String inputLogCalendarFile = readFromFile("input_log_calendar");
        if(!inputLogString.isEmpty()) {
            String[] inputLogItems = inputLogString.split("\\|");
            for(int i=0;i<inputLogItems.length;i++) {
                String[] inputLogItemParts = inputLogItems[i].split(";");
                oInputLog tLog = new oInputLog();
                tLog.inputLogId = Integer.parseInt(inputLogItemParts[15]);
                tLog.inputLogFieldId = Integer.parseInt(inputLogItemParts[0]);
                tLog.inputLogPlots = inputLogItemParts[1];
                tLog.inputLogUserId = Integer.parseInt(inputLogItemParts[2]);
                tLog.inputLogCropId = Integer.parseInt(inputLogItemParts[3]);
                tLog.inputLogTreatmentId = Integer.parseInt(inputLogItemParts[4]);
                tLog.inputLogDate = stringToDate(inputLogItemParts[5]);
                tLog.inputLogInputAge = inputLogItemParts[6];
                tLog.inputLogInputOrigin = inputLogItemParts[7];
                tLog.inputLogCropVariety = inputLogItemParts[8];
                tLog.inputLogInputQuantity = Float.parseFloat(inputLogItemParts[9]);
                tLog.inputLogInputUnits = inputLogItemParts[10];
                tLog.inputLogInputCost = inputLogItemParts[11];
                tLog.inputLogTreatmentMaterial = inputLogItemParts[12];
                tLog.inputLogTreatmentPreparationMethod = inputLogItemParts[13];
                tLog.inputLogComments = inputLogItemParts[14];
                inputLog.add(tLog);
            }
        }
        if(!inputLogCalendarFile.isEmpty()) {
            String[] inputLogCalendarLines = inputLogCalendarFile.split(";");
            for (int i = 0; i < inputLogCalendarLines.length; i++) {
                oInputLogCalendar ilC = new oInputLogCalendar();
                String[] inputLogCalendarParts = inputLogCalendarLines[i].split(",");
                ilC.cropId = Integer.parseInt(inputLogCalendarParts[0]);
                ilC.treatmentId = Integer.parseInt(inputLogCalendarParts[1]);
                ilC.plotN = Integer.parseInt(inputLogCalendarParts[2]);
                ilC.fieldId = Integer.parseInt(inputLogCalendarParts[3]);
                ilC.date = inputLogCalendarParts[4];
                inputLogCalendar.add(ilC);
            }
        }
    }

    public void sortLog(){
        boolean sort=true;
        oLog tempLog1;
        oLog tempLog2;

        while(sort){
            sort=false;
            for(int i=0;i<log.size()-1;i++){
                tempLog1=log.get(i);
                tempLog2=log.get(i+1);
                if(tempLog1.logDate.after(tempLog2.logDate)){
                    log.remove(i);
                    log.add(i,tempLog2);
                    log.remove(i+1);
                    log.add(i+1,tempLog1);
                    sort=true;
                }
            }
        }
    }

    public void sortInputLog(){
        boolean sort=true;
        oInputLog tempLog1;
        oInputLog tempLog2;

        while(sort){
            sort=false;
            for(int i=0;i<inputLog.size()-1;i++){
                tempLog1=inputLog.get(i);
                tempLog2=inputLog.get(i+1);
                if(tempLog1.inputLogDate.after(tempLog2.inputLogDate)){
                    inputLog.remove(i);
                    inputLog.add(i,tempLog2);
                    inputLog.remove(i+1);
                    inputLog.add(i+1,tempLog1);
                    sort=true;
                }
            }
        }
    }

    public oCrop getCropFromId(int id){
        oCrop ret=null;
        Iterator<oCrop> iterator = crops.iterator();
        while(iterator.hasNext()){
            oCrop c = iterator.next();
            if(c.cropId==id){
                ret=c;
                break;
            }
        }
        return ret;
    }

    public oTreatment getTreatmentFromId(int id){
        oTreatment ret=null;
        Iterator<oTreatment> iterator = treatments.iterator();
        while(iterator.hasNext()){
            oTreatment t = iterator.next();
            if(t.treatmentId==id){
                ret=t;
                break;
            }
        }
        return ret;
    }

    public void createFields(){
        fields=new ArrayList<>();
        List<String[]> fieldsCSV = readCSVFile("fields");
        if(fieldsCSV!=null) {
            Iterator<String[]> iterator = fieldsCSV.iterator();
            while (iterator.hasNext()) {
                String[] record = iterator.next();
                oField field = new oField();
                field.fieldId = Integer.parseInt(record[0]);
                field.parentFieldId = Integer.parseInt(record[1]);
                field.fieldName = record[4];
                field.fieldReplicationN = Integer.parseInt(record[5]);
                field.plots = parsePlots(record[8]);
                String[] grid = getFieldRowsColumns(record[8]);
                boolean[] fieldTreatments = getFieldTreatments(record[8]);
                field.hasIntercropping = fieldTreatments[0];
                field.hasSoilManagement = fieldTreatments[1];
                field.hasPestControl = fieldTreatments[2];
                field.rows = Integer.parseInt(grid[0]);
                field.columns = Integer.parseInt(grid[1]);
                fields.add(field);
            }
        }
    }

    public boolean[] getFieldTreatments(String d){
        String[] plotParts = d.split(";");
        String sub = plotParts[0].substring(3,10);
        String[] defParts = sub.split(",");
        boolean ret[] = {(!defParts[0].equals("0")),(defParts[1].equals("1")),(defParts[2].equals("1"))};
        return ret;
    }

    public oField getFieldFromId(int id){
        oField ret=null;
        Iterator<oField> iterator = fields.iterator();
        while (iterator.hasNext()) {
            oField field = iterator.next();
            if(field.fieldId==id){
                ret=field;
                break;
            }
        }
        return ret;
    }

    public String getFieldNameFromId(int id){
        oField f = getFieldFromId(id);
        return f.fieldName + " R" + Integer.toString(f.fieldReplicationN);
    }

    public String[] getFieldRowsColumns(String plotsString){
        String[] plotParts = plotsString.split(";");
        String sub = plotParts[1].substring(3,6);
        String[] ret = sub.split(",");
        return ret;
    }

    public ArrayList<oPlot> parsePlots(String plotsString){
        ArrayList<oPlot> ret=new ArrayList<>();
        String[] plotParts = plotsString.split(";");

        for(int i=2;i<plotParts.length;i++){
            String[] plotElements = parsePlotElement(plotParts[i]);
            oPlot plot = new oPlot();
            plot.plotNumber=i-1;
            oCrop plotPrimaryCrop = getCropFromId(Integer.parseInt(plotElements[0]));
            plot.primaryCrop=plotPrimaryCrop;
            if(Integer.parseInt(plotElements[1])==0){
                plot.intercroppingCrop=null;
            } else {
                oCrop plotIntercroppingCrop = getCropFromId(Integer.parseInt(plotElements[1]));
                plot.intercroppingCrop = plotIntercroppingCrop;
            }
            plot.hasSoilManagement=(Integer.parseInt(plotElements[2])==1);
            plot.hasPestControl=(Integer.parseInt(plotElements[3])==1);
            ret.add(plot);
        }
        return ret;
    }

    public String[] parsePlotElement(String plotString){
        String[] ret;
        String sub = plotString.substring(3,10);
        ret = sub.split(",");
        return ret;
    }

    public boolean isPlotChooseable(oPlot p, String task, String subTask, int taskId){
        boolean ret=false;
        if(task.equals("activity")){
            oActivity a=getActivityFromId(taskId);
            if (a.activityAppliesToCrops.size() == 0 && a.activityAppliesToTreatments.size() == 0) {
                ret=true;
            } else {
                oCrop plotCrop = p.primaryCrop;
                oCrop plotCropIntercropping = p.intercroppingCrop;
                ArrayList<oCrop> appliedCrops = a.activityAppliesToCrops;
                Iterator<oCrop> iteratorCrop = appliedCrops.iterator();
                while (iteratorCrop.hasNext()) {
                    oCrop aC = iteratorCrop.next();
                    if (aC.cropId == plotCrop.cropId || (plotCropIntercropping!=null && aC.cropId==plotCropIntercropping.cropId)) {
                        ret=true;
                        break;
                    }
                }

                Iterator<oTreatment> iteratorTreatment = a.activityAppliesToTreatments.iterator();
                while (iteratorTreatment.hasNext()) {
                    oTreatment aT = iteratorTreatment.next();
                    if ((p.intercroppingCrop != null && aT.treatmentCategory.equals("Intercropping"))
                            || (p.hasSoilManagement && aT.treatmentCategory.equals("Soil management"))
                            || (p.hasPestControl && aT.treatmentCategory.equals("Pest control"))) {
                        ret=true;
                        break;
                    }
                }
            }
        } else if(task.equals("measurement")){
            oMeasurement m=getMeasurementFromId(taskId);
            if(m.measurementAppliesToCrops.size()==0 && m.measurementAppliesToTreatments.size()==0){
                ret=true;
            } else {
                oCrop plotCrop = p.primaryCrop;
                oCrop plotCropIntercropping = p.intercroppingCrop;
                Iterator<oCrop> iteratorCrop = m.measurementAppliesToCrops.iterator();
                while (iteratorCrop.hasNext()) {
                    oCrop mC = iteratorCrop.next();
                    if ((mC.cropId == plotCrop.cropId) || (plotCropIntercropping!=null && mC.cropId == plotCropIntercropping.cropId)) {
                        ret=true;
                        break;
                    }
                }

                Iterator<oTreatment> iteratorTreatment = m.measurementAppliesToTreatments.iterator();
                while (iteratorTreatment.hasNext()) {
                    oTreatment mT = iteratorTreatment.next();
                    if ((p.intercroppingCrop != null && mT.treatmentCategory.equals("Intercropping"))
                            || (p.hasSoilManagement && mT.treatmentCategory.equals("Soil management"))
                            || (p.hasPestControl && mT.treatmentCategory.equals("Pest control"))) {
                        ret=true;
                        break;
                    }
                }
            }
        } else if(task.equals("input")){
            if(subTask.equals("crop")){
                oCrop plotCrop = p.primaryCrop;
                oCrop plotCropIntercropping = p.intercroppingCrop;
                if(plotCrop.cropId==taskId || (plotCropIntercropping!=null && plotCropIntercropping.cropId==taskId)){
                    ret=true;
                }
            } else if(subTask.equals("treatment")){
                oTreatment t = getTreatmentFromId(taskId);
                if ((p.intercroppingCrop != null && t.treatmentCategory.equals("Intercropping"))
                        || (p.hasSoilManagement && t.treatmentCategory.equals("Soil management"))
                        || (p.hasPestControl && t.treatmentCategory.equals("Pest control"))) {
                    ret=true;
                }
            }
        }
        return ret;
    }

    public ArrayList<oPlot> getPlots(int fieldId, String p){
        oField f = getFieldFromId(fieldId);
        ArrayList<oPlot> ret= new ArrayList<>();
        String[] plots = p.split(",");
        for(int i=0;i<plots.length;i++){
            oPlot plot = f.plots.get(Integer.valueOf(plots[i]));
            ret.add(plot);
        }
        return ret;
    }

    public oPlot getModelPlot(ArrayList<oPlot> plots){
        oPlot ret = new oPlot();
        boolean hasPestControl=true;
        boolean hasSoilManagement=true;
        int prevCrop=0;
        int prevIntercroppingCrop=0;
        Iterator<oPlot> plotIterator = plots.iterator();
        int i=0;
        while (plotIterator.hasNext()) {
            oPlot plot = plotIterator.next();
            if(prevCrop==0){
                prevCrop=plot.primaryCrop.cropId;
                ret.primaryCrop=plot.primaryCrop;
            } else {
                if(prevCrop!=plot.primaryCrop.cropId){
                    ret.primaryCrop=null;
                }
            }
            if(plot.intercroppingCrop!=null) {
                if (prevIntercroppingCrop == 0) {
                    if(i==0) {
                        prevIntercroppingCrop = plot.intercroppingCrop.cropId;
                        ret.intercroppingCrop = plot.intercroppingCrop;
                    } else {
                        ret.intercroppingCrop=null;
                    }
                } else {
                    if (prevIntercroppingCrop != plot.intercroppingCrop.cropId) {
                        ret.intercroppingCrop = null;
                    }
                }
            } else if(prevIntercroppingCrop!=0){
                ret.intercroppingCrop=null;
            }
            if(!plot.hasPestControl){
                hasPestControl=false;
            }
            if(!plot.hasSoilManagement){
                hasSoilManagement=false;
            }
            i++;
        }
        ret.hasPestControl = hasPestControl;
        ret.hasSoilManagement = hasSoilManagement;
        return ret;
    }

    public oActivity getActivityFromId(int id){
        oActivity ret = new oActivity();
        Iterator<oActivity> iterator = activities.iterator();
        while (iterator.hasNext()) {
            oActivity activity = iterator.next();
            if(activity.activityId==id){
                ret=activity;
                break;
            }
        }
        return ret;
    }

    public ArrayList<oActivity> getActivitiesForPlots(int fieldId, String p){
        ArrayList<oActivity> ret = new ArrayList<>();
        ArrayList<oPlot> plots = getPlots(fieldId,p);

        oPlot modelPlot = getModelPlot(plots);

        Iterator<oActivity> activityIterator = activities.iterator();
        while (activityIterator.hasNext()) {
            oActivity activity = activityIterator.next();
            if (activity.activityAppliesToCrops.size() == 0 && activity.activityAppliesToTreatments.size() == 0) {
                if(!ret.contains(activity)){
                    ret.add(activity);
                }
            } else {

                boolean cropOK = false;
                boolean treatmentOK = false;

                if(activity.activityAppliesToTreatments.size()>0) {
                    Iterator<oTreatment> iteratorTreatment = activity.activityAppliesToTreatments.iterator();
                    while (iteratorTreatment.hasNext()) {
                        oTreatment aT = iteratorTreatment.next();
                        if ((modelPlot.intercroppingCrop != null && aT.treatmentCategory.equals("Intercropping"))
                                || (modelPlot.hasSoilManagement && aT.treatmentCategory.equals("Soil management"))
                                || (modelPlot.hasPestControl && aT.treatmentCategory.equals("Pest control"))) {
                            treatmentOK = true;
                            break;
                        }
                    }
                } else {
                    treatmentOK = true;
                }

                if (activity.activityAppliesToCrops.size() > 0) {
                    Iterator<oCrop> iteratorCrop = activity.activityAppliesToCrops.iterator();
                    while (iteratorCrop.hasNext()) {
                        oCrop aC = iteratorCrop.next();
                        if(modelPlot.primaryCrop!=null) {
                            if (aC.cropId == modelPlot.primaryCrop.cropId) {
                                cropOK = true;
                                break;
                            }
                        }
                    }
                } else{
                    cropOK = true;
                }


                if ((cropOK && treatmentOK) && !ret.contains(activity)) {
                    ret.add(activity);
                }
            }
        }
        return ret;
    }

    public boolean fieldIdExists(int fieldId){
        boolean ret=false;
        Iterator<oField> iterator = fields.iterator();
        while(iterator.hasNext()){
            oField f = iterator.next();
            if(f.fieldId==fieldId){
                ret=true;
                break;
            }
        }
        return ret;
    }

    public ArrayList<oActivity> getActivities(oPlot plot, oField field){
        ArrayList<oActivity> ret = new ArrayList<>();
        if(plot!=null) {
            Iterator<oActivity> iterator = activities.iterator();
            while (iterator.hasNext()) {
                oActivity activity = iterator.next();
                if (activity.activityAppliesToCrops.size() == 0 && activity.activityAppliesToTreatments.size() == 0) {
                    ret.add(activity);
                } else {
                    oCrop plotCrop = plot.primaryCrop;
                    Iterator<oCrop> iteratorCrop = activity.activityAppliesToCrops.iterator();
                    while (iteratorCrop.hasNext()) {
                        oCrop aC = iteratorCrop.next();
                        if (aC.cropId == plotCrop.cropId) {
                            if (!ret.contains(activity)) {
                                ret.add(activity);
                            }
                        }
                    }

                    Iterator<oTreatment> iteratorTreatment = activity.activityAppliesToTreatments.iterator();
                    while (iteratorTreatment.hasNext()) {
                        oTreatment aT = iteratorTreatment.next();
                        if ((plot.intercroppingCrop != null && aT.treatmentCategory.equals("Intercropping"))
                                || (plot.hasSoilManagement && aT.treatmentCategory.equals("Soil management"))
                                || (plot.hasPestControl && aT.treatmentCategory.equals("Pest control"))) {
                            if (!ret.contains(activity)) {
                                ret.add(activity);
                            }
                        }
                    }
                }
            }
        } else if(field!=null){
            Iterator<oActivity> iterator = activities.iterator();
            while (iterator.hasNext()) {
                oActivity activity = iterator.next();
                if (activity.activityAppliesToTreatments.size() == 0) {
                    ret.add(activity);
                } else {
                    Iterator<oTreatment> iteratorTreatment = activity.activityAppliesToTreatments.iterator();
                    while (iteratorTreatment.hasNext()) {
                        oTreatment aT = iteratorTreatment.next();
                        if((field.hasIntercropping && aT.treatmentCategory.equals("Intercropping"))
                            || (field.hasSoilManagement && aT.treatmentCategory.equals("Soil management"))
                            || (field.hasPestControl && aT.treatmentCategory.equals("Pest control"))) {
                            if(!ret.contains(activity)) {
                                ret.add(activity);
                            }
                        }
                    }
                }
            }
        } else {
            ret=activities;
        }
        return ret;
    }

    public ArrayList<oMeasurement> getMeasurements(oPlot plot, oField field, int userRole){
        ArrayList<oMeasurement> ret = new ArrayList<>();
        if(plot!=null) {
            Iterator<oMeasurement> iterator = measurements.iterator();
            while (iterator.hasNext()) {
                oMeasurement measurement = iterator.next();
                if (measurement.measurementAppliesToCrops.size() == 0 && measurement.measurementAppliesToTreatments.size() == 0) {
                    if(measurement.measurementIsCommon || (!measurement.measurementIsCommon && userRole>0)) {
                        ret.add(measurement);
                    }
                } else {
                    oCrop plotCrop = plot.primaryCrop;
                    Iterator<oCrop> iteratorCrop = measurement.measurementAppliesToCrops.iterator();
                    while (iteratorCrop.hasNext()) {
                        oCrop aC = iteratorCrop.next();
                        if (aC.cropId == plotCrop.cropId) {
                            if (!ret.contains(measurement)) {
                                if(measurement.measurementIsCommon || (!measurement.measurementIsCommon && userRole>0)) {
                                    ret.add(measurement);
                                }
                            }
                        }
                    }

                    Iterator<oTreatment> iteratorTreatment = measurement.measurementAppliesToTreatments.iterator();
                    while (iteratorTreatment.hasNext()) {
                        oTreatment aT = iteratorTreatment.next();
                        if ((plot.intercroppingCrop != null && aT.treatmentCategory.equals("Intercropping"))
                                || (plot.hasSoilManagement && aT.treatmentCategory.equals("Soil management"))
                                || (plot.hasPestControl && aT.treatmentCategory.equals("Pest control"))) {
                            if (!ret.contains(measurement)) {
                                if(measurement.measurementIsCommon || (!measurement.measurementIsCommon && userRole>0)) {
                                    ret.add(measurement);
                                }
                            }
                        }
                    }
                }
            }
        } else if(field!=null){
            Iterator<oMeasurement> iterator = measurements.iterator();
            while (iterator.hasNext()) {
                oMeasurement measurement = iterator.next();
                if (measurement.measurementAppliesToTreatments.size() == 0) {
                    if(measurement.measurementIsCommon || (!measurement.measurementIsCommon && userRole>0)) {
                        ret.add(measurement);
                    }
                } else {
                    Iterator<oTreatment> iteratorTreatment = measurement.measurementAppliesToTreatments.iterator();
                    while (iteratorTreatment.hasNext()) {
                        oTreatment aT = iteratorTreatment.next();
                        if((field.hasIntercropping && aT.treatmentCategory.equals("Intercropping"))
                                || (field.hasSoilManagement && aT.treatmentCategory.equals("Soil management"))
                                || (field.hasPestControl && aT.treatmentCategory.equals("Pest control"))) {
                            if(!ret.contains(measurement)) {
                                if(measurement.measurementIsCommon || (!measurement.measurementIsCommon && userRole>0)) {
                                    ret.add(measurement);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            Iterator<oMeasurement> iterator = measurements.iterator();
            while (iterator.hasNext()) {
                oMeasurement measurement = iterator.next();
                if(measurement.measurementIsCommon || (!measurement.measurementIsCommon && userRole>0)){
                    ret.add(measurement);
                }
            }
        }
        return ret;
    }

    public int getDaysAgo(Date d){
        Calendar thisDate = Calendar.getInstance();
        thisDate.setTime(new Date());
        Calendar pastDate = Calendar.getInstance();
        pastDate.setTime(d);
        long msDiff = thisDate.getTimeInMillis() - pastDate.getTimeInMillis();
        float dayCount = (float) msDiff / (24 * 60 * 60 * 1000);
        int ret = (int)dayCount;
        return ret;
    }

    public Date stringToDate(String d){
        Date date = new Date();
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        sdf.setTimeZone(TimeZone.getDefault());
        try {
            date = sdf.parse(d);
        } catch (ParseException e) {

        }
        return date;
    }

    public String dateToString(Date d){
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        sdf.setTimeZone(TimeZone.getDefault());
        return sdf.format(d);
    }

    public ArrayList<oCrop> sortCropListBySymbol(ArrayList<oCrop> cList){
        Collections.sort(cList, new Comparator<oCrop>() {
            @Override
            public int compare(oCrop c1, oCrop c2) {
                return c1.cropSymbol.compareTo(c2.cropSymbol);
            }
        });
        return cList;
    }

    public ArrayList<oField> getReplicationsFromFieldId(int id){
        ArrayList<oField> ret = new ArrayList<>();

        oField sourceField = getFieldFromId(id);

        Iterator<oField> iterator = fields.iterator();
        while (iterator.hasNext()) {
            oField field = iterator.next();
            if(field.fieldId!=id && field.parentFieldId==sourceField.parentFieldId){
                ret.add(field);
            }
        }
        return ret;
    }

    public boolean plotsAreEqual(oPlot source, oPlot dest){
        boolean ret=false;
        if(source.primaryCrop.cropId==dest.primaryCrop.cropId){
            if((source.intercroppingCrop==null && dest.intercroppingCrop!=null) || (source.intercroppingCrop!=null && dest.intercroppingCrop==null)){

            } else {
                if ((source.intercroppingCrop == null && dest.intercroppingCrop == null) || (source.intercroppingCrop.cropId == dest.intercroppingCrop.cropId)) {
                    if (source.hasPestControl == dest.hasPestControl && source.hasSoilManagement == dest.hasSoilManagement) {
                        ret = true;
                    }
                }
            }
        }
        return ret;
    }


    public String getDestinationPlots(String sourcePlots, int sourceFieldId, oField field){
        String ret="";
        oField sourceField = getFieldFromId(sourceFieldId);
        String sourcePlotsList[]=sourcePlots.split(",");
        for(int i=0;i<sourcePlotsList.length;i++) {
            oPlot sourcePlot = sourceField.plots.get(Integer.valueOf(sourcePlotsList[i]));
            Iterator<oPlot> iterator = field.plots.iterator();
            int n = 0;
            while (iterator.hasNext()) {
                oPlot destPlot = iterator.next();
                if (plotsAreEqual(sourcePlot, destPlot)) {
                    if (ret.isEmpty()) {
                        ret = String.valueOf(n);
                    } else {
                        ret = ret + "," + String.valueOf(n);
                    }
                }
                n++;
            }
        }
        return ret;
    }

    public void addActivityToLog(int fieldId, String plots, int userId, int activityId, String date, float numberValue, String units, String laborers, String cost, String comments, boolean copy){
        //updateActivityDaysAgo(activityId, plotN, fieldId, date);
        createLog();
        oLog newEntry = new oLog();
        newEntry.logId = getNewLogId();
        newEntry.logFieldId = fieldId;
        newEntry.logPlots = plots;
        newEntry.logUserId = userId;
        newEntry.logActivityId = activityId;
        newEntry.logDate = stringToDate(date);
        newEntry.logNumberValue = numberValue;
        newEntry.logValueUnits = units;
        newEntry.logLaborers = laborers;
        newEntry.logCost = cost;
        newEntry.logComments = comments;
        log.add(newEntry);

        if(copy){
            ArrayList<oField> replications = getReplicationsFromFieldId(fieldId);
            if(replications!=null){
                Iterator<oField> iterator = replications.iterator();
                while (iterator.hasNext()) {
                    oField field = iterator.next();
                    String destPlots = getDestinationPlots(plots, fieldId, field);
                    if(!destPlots.isEmpty()) {
                        newEntry = new oLog();
                        newEntry.logId = getNewLogId();
                        newEntry.logFieldId = field.fieldId;
                        newEntry.logPlots = destPlots;
                        newEntry.logUserId = userId;
                        newEntry.logActivityId = activityId;
                        newEntry.logDate = stringToDate(date);
                        newEntry.logNumberValue = numberValue;
                        newEntry.logValueUnits = units;
                        newEntry.logLaborers = laborers;
                        newEntry.logCost = cost;
                        newEntry.logComments = comments+" (copied)";
                        log.add(newEntry);
                    }
                }
            }
        }

        sortLog();
        writeLog();
    }

    public void addCropToInputLog(int fieldId, String plots, int userId, int cropId, String date, String age, String origin, String variety, float quantity, String units, String cost, String comments, boolean copy){
        //updateCropInputDaysAgo(cropId, plotN, fieldId, date);
        createInputLog();
        oInputLog newEntry = new oInputLog();
        newEntry.inputLogId = getNewInputLogId();
        newEntry.inputLogFieldId = fieldId;
        newEntry.inputLogPlots = plots;
        newEntry.inputLogUserId = userId;
        newEntry.inputLogCropId = cropId;
        newEntry.inputLogDate = stringToDate(date);
        newEntry.inputLogInputAge = age;
        newEntry.inputLogInputOrigin = origin;
        newEntry.inputLogCropVariety = variety;
        newEntry.inputLogInputQuantity = quantity;
        newEntry.inputLogInputUnits = units;
        newEntry.inputLogInputCost = cost;
        newEntry.inputLogComments = comments;
        inputLog.add(newEntry);

        if(copy){
            ArrayList<oField> replications = getReplicationsFromFieldId(fieldId);
            if(replications!=null) {
                Iterator<oField> iterator = replications.iterator();
                while (iterator.hasNext()) {
                    oField field = iterator.next();
                    String destPlots = getDestinationPlots(plots, fieldId, field);
                    if (!destPlots.isEmpty()) {
                        newEntry = new oInputLog();
                        newEntry.inputLogId = getNewInputLogId();
                        newEntry.inputLogFieldId = field.fieldId;
                        newEntry.inputLogPlots = destPlots;
                        newEntry.inputLogUserId = userId;
                        newEntry.inputLogCropId = cropId;
                        newEntry.inputLogDate = stringToDate(date);
                        newEntry.inputLogInputAge = age;
                        newEntry.inputLogInputOrigin = origin;
                        newEntry.inputLogCropVariety = variety;
                        newEntry.inputLogInputQuantity = quantity;
                        newEntry.inputLogInputUnits = units;
                        newEntry.inputLogInputCost = cost;
                        newEntry.inputLogComments = comments+" (copied)";
                        inputLog.add(newEntry);
                    }
                }
            }
        }

        sortInputLog();
        writeInputLog();
    }

    public void addTreatmentToInputLog(int fieldId, String plots, int userId, int treatmentId, String date, String material, float quantity, String units, String method, String cost, String comments, boolean copy){
        //updateTreatmentInputDaysAgo(treatmentId, plotN, fieldId, date);
        createInputLog();
        oInputLog newEntry = new oInputLog();
        newEntry.inputLogId = getNewInputLogId();
        newEntry.inputLogFieldId = fieldId;
        newEntry.inputLogPlots = plots;
        newEntry.inputLogUserId = userId;
        newEntry.inputLogTreatmentId = treatmentId;
        newEntry.inputLogDate = stringToDate(date);
        newEntry.inputLogTreatmentMaterial = material;
        newEntry.inputLogInputQuantity = quantity;
        newEntry.inputLogInputUnits = units;
        newEntry.inputLogTreatmentPreparationMethod = method;
        newEntry.inputLogInputCost = cost;
        newEntry.inputLogComments = comments;
        inputLog.add(newEntry);

        if(copy){
            ArrayList<oField> replications = getReplicationsFromFieldId(fieldId);
            if(replications!=null) {
                Iterator<oField> iterator = replications.iterator();
                while (iterator.hasNext()) {
                    oField field = iterator.next();
                    String destPlots = getDestinationPlots(plots, fieldId, field);
                    if (!destPlots.isEmpty()) {
                        newEntry = new oInputLog();
                        newEntry.inputLogId = getNewInputLogId();
                        newEntry.inputLogFieldId = field.fieldId;
                        newEntry.inputLogPlots = destPlots;
                        newEntry.inputLogUserId = userId;
                        newEntry.inputLogTreatmentId = treatmentId;
                        newEntry.inputLogDate = stringToDate(date);
                        newEntry.inputLogTreatmentMaterial = material;
                        newEntry.inputLogInputQuantity = quantity;
                        newEntry.inputLogInputUnits = units;
                        newEntry.inputLogTreatmentPreparationMethod = method;
                        newEntry.inputLogInputCost = cost;
                        newEntry.inputLogComments = comments+" (copied)";
                        inputLog.add(newEntry);
                    }
                }
            }
        }

        sortInputLog();
        writeInputLog();
    }

    public void addMeasurementToLog(int fieldId, String plots, int userId, int measurementId, String date, float numberValue, String units, String category, String comments){
        //updateMeasurementDaysAgo(measurementId, plotN, fieldId, date);
        createLog();
        oLog newEntry = new oLog();
        newEntry.logId = getNewLogId();
        newEntry.logFieldId = fieldId;
        newEntry.logPlots = plots;
        newEntry.logUserId = userId;
        newEntry.logMeasurementId = measurementId;
        newEntry.logDate = stringToDate(date);
        newEntry.logNumberValue = numberValue;
        newEntry.logValueUnits = units;
        newEntry.logTextValue = category;
        newEntry.logComments = comments;
        log.add(newEntry);
        sortLog();
        writeLog();
    }

    public int getNewLogId(){
        int ret=-1;
        Iterator<oLog> iterator = log.iterator();
        while(iterator.hasNext()){
            oLog l = iterator.next();
            if(l.logId>ret){
                ret=l.logId;
            }
        }
        return ret+1;
    }

    public int getNewInputLogId(){
        int ret=-1;
        Iterator<oInputLog> iterator = inputLog.iterator();
        while(iterator.hasNext()){
            oInputLog l = iterator.next();
            if(l.inputLogId>ret){
                ret=l.inputLogId;
            }
        }
        return ret+1;
    }

    public oLog getLogItemFromId(int id){
        oLog ret=null;
        Iterator<oLog> iterator = log.iterator();
        while(iterator.hasNext()){
            oLog l = iterator.next();
            if(l.logId==id){
                ret=l;
                break;
            }
        }
        return ret;
    }

    public oInputLog getInputLogItemFromId(int id){
        oInputLog ret=null;
        Iterator<oInputLog> iterator = inputLog.iterator();
        while(iterator.hasNext()){
            oInputLog l = iterator.next();
            if(l.inputLogId==id){
                ret=l;
                break;
            }
        }
        return ret;
    }

    public void updateLogActivityEntry(int logId, String aD, Float aV, String aU, String aL, String aK, String aC){
        Iterator<oLog> iterator = log.iterator();
        while(iterator.hasNext()){
            oLog l = iterator.next();
            if(l.logId==logId){
                l.logDate=stringToDate(aD);
                l.logNumberValue=aV;
                l.logValueUnits=aU;
                l.logLaborers=aL;
                l.logCost=aK;
                l.logComments=aC;
                //updateActivityDaysAgo(l.logActivityId,l.logPlotNumber,l.logFieldId,aD);
                sortLog();
                writeLog();
                break;
            }
        }
    }

    public void updateInputLogCropEntry(int inputLogId, String cD, String cA, String cO, String cV, float cQ, String cU, String cC, String cCC){
        Iterator<oInputLog> iterator = inputLog.iterator();
        while(iterator.hasNext()){
            oInputLog l = iterator.next();
            if(l.inputLogId==inputLogId){
                l.inputLogDate=stringToDate(cD);
                l.inputLogInputAge=cA;
                l.inputLogInputOrigin=cO;
                l.inputLogCropVariety=cV;
                l.inputLogInputQuantity=cQ;
                l.inputLogInputUnits=cU;
                l.inputLogInputCost=cC;
                l.inputLogComments=cCC;
                //updateCropInputDaysAgo(l.inputLogCropId,l.inputLogPlotNumber,l.inputLogFieldId,cD);
                sortInputLog();
                writeInputLog();
                break;
            }
        }
    }

    public void updateInputLogTreatmentEntry(int inputLogId, String tD, String tM, float tQ, String tU, String tMM, String tC, String tCC){
        Iterator<oInputLog> iterator = inputLog.iterator();
        while(iterator.hasNext()){
            oInputLog l = iterator.next();
            if(l.inputLogId==inputLogId){
                l.inputLogDate=stringToDate(tD);
                l.inputLogTreatmentMaterial=tM;
                l.inputLogInputQuantity=tQ;
                l.inputLogInputUnits=tU;
                l.inputLogTreatmentPreparationMethod=tMM;
                l.inputLogInputCost=tC;
                l.inputLogComments=tCC;
                //updateTreatmentInputDaysAgo(l.inputLogTreatmentId,l.inputLogPlotNumber,l.inputLogFieldId,tD);
                sortInputLog();
                writeInputLog();
                break;
            }
        }
    }

    public void updateLogMeasurementEntry(int logId, int mS, String mD, Float mV, String mU, String mC, String mCC){
        Iterator<oLog> iterator = log.iterator();
        while(iterator.hasNext()){
            oLog l = iterator.next();
            if(l.logId==logId){
                l.logSampleNumber = mS;
                l.logDate=stringToDate(mD);
                l.logNumberValue=mV;
                l.logValueUnits=mU;
                l.logTextValue=mC;
                l.logComments=mCC;
                //updateMeasurementDaysAgo(l.logMeasurementId,l.logPlotNumber,l.logFieldId,mD);
                sortLog();
                writeLog();
                break;
            }
        }
    }

    public oMeasuredPlotHelper getMeasuredPlotFromLogId(int id, int fId, int mId, String p){
        oMeasuredPlotHelper mp = new oMeasuredPlotHelper(-1,-1,-1);
        Iterator<oLog> iterator = log.iterator();
        while(iterator.hasNext()) {
            oLog l = iterator.next();
            if(l.logId==id && l.logFieldId==fId && l.logMeasurementId==mId && l.logPlots.equals(p)){
                mp.fieldId=l.logFieldId;
                mp.plotNumber=Integer.valueOf(l.logPlots);
                mp.measurementId=l.logMeasurementId;
                break;
            }
        }
        return mp;
    }

    public void deleteLogEntries(String e, boolean deleteFromCalendar){
        boolean bWriteActivitiesCalendar=false;
        boolean bWriteMeasurementsCalendar=false;
        String[] entries = e.split(",");
        for(int i=0; i<entries.length; i++){
            int id=Integer.parseInt(entries[i]);
            Iterator<oLog> iterator = log.iterator();
            int n=0;
            while(iterator.hasNext()) {
                oLog l = iterator.next();
                if(l.logId==id){
                    if(l.logActivityId>0 && deleteFromCalendar){
                        deleteActivityFromCalendar(l.logActivityId,l.logPlotNumber,l.logFieldId);
                        bWriteActivitiesCalendar=true;
                    } else if(l.logMeasurementId>0 && deleteFromCalendar){
                        deleteMeasurementFromCalendar(l.logMeasurementId,l.logPlotNumber,l.logFieldId);
                        bWriteMeasurementsCalendar=true;
                    }
                    log.remove(n);
                    break;
                }
                n++;
            }
        }
        if(bWriteActivitiesCalendar) { writeActivitiesCalendarFile(); }
        if(bWriteMeasurementsCalendar) { writeMeasurementsCalendarFile(); }
        sortLog();
        writeLog();
    }

    public void deleteInputLogEntries(String e, boolean deleteFromCalendar){
        boolean bWriteCalendar=false;
        String[] entries = e.split(",");
        for(int i=0; i<entries.length; i++){
            int id=Integer.parseInt(entries[i]);
            Iterator<oInputLog> iterator = inputLog.iterator();
            int n=0;
            while(iterator.hasNext()) {
                oInputLog l = iterator.next();
                if(l.inputLogId==id){
                    if(l.inputLogCropId>0 && deleteFromCalendar){
                        deleteCropFromCalendar(l.inputLogCropId,l.inputLogPlotNumber,l.inputLogFieldId);
                        bWriteCalendar=true;
                    } else if(l.inputLogTreatmentId>0 && deleteFromCalendar){
                        deleteTreatmentFromCalendar(l.inputLogTreatmentId,l.inputLogPlotNumber,l.inputLogFieldId);
                        bWriteCalendar=true;
                    }
                    inputLog.remove(n);
                    break;
                }
                n++;
            }
        }
        if(bWriteCalendar) { writeInputLogCalendarFile(); }
        sortInputLog();
        writeInputLog();
    }

    public String getSelectedLogItemsAsString(String selected){
        String ret="";
        String[] entries = selected.split(",");
        for(int i=0; i<entries.length; i++){
            int id=Integer.parseInt(entries[i]);
            Iterator<oLog> iterator = log.iterator();
            while(iterator.hasNext()) {
                oLog l = iterator.next();
                if(l.logId==id){
                    ret+=Integer.toString(l.logFieldId)+";"+l.logPlots+";"+Integer.toString(l.logUserId)+";"+Integer.toString(l.logCropId)
                            +";"+Integer.toString(l.logTreatmentId)+";"+Integer.toString(l.logMeasurementId)+";"+Integer.toString(l.logActivityId)
                            +";"+dateToString(l.logDate)+";"+Float.toString(l.logNumberValue)+";"+l.logValueUnits+";"+l.logTextValue+";"+l.logLaborers
                            +";"+l.logCost+";"+l.logComments+";"+Integer.toString(l.logId)+";"+Integer.toString(l.logSampleNumber)+"|";
                    break;
                }
            }
        }
        return ret;
    }

    public String getSelectedInputLogItemsAsString(String selected){
        String ret="";
        String[] entries = selected.split(",");
        for(int i=0; i<entries.length; i++){
            int id=Integer.parseInt(entries[i]);
            Iterator<oInputLog> iterator = inputLog.iterator();
            while(iterator.hasNext()) {
                oInputLog l = iterator.next();
                if(l.inputLogId==id){
                    ret+=Integer.toString(l.inputLogId)+";"+Integer.toString(l.inputLogFieldId)+";"+l.inputLogPlots+";"
                            +Integer.toString(l.inputLogUserId)+";"+Integer.toString(l.inputLogCropId)+";"+Integer.toString(l.inputLogTreatmentId)
                            +";"+dateToString(l.inputLogDate)+";"+l.inputLogInputAge+";"+l.inputLogInputOrigin+";"+l.inputLogCropVariety+";"
                            +Float.toString(l.inputLogInputQuantity)+";"+l.inputLogInputUnits+";"+l.inputLogInputCost+";"+l.inputLogTreatmentMaterial
                            +";"+l.inputLogTreatmentPreparationMethod+";"+l.inputLogComments+"|";
                    break;
                }
            }
        }
        return ret;
    }

    public void deleteCropFromCalendar(int id, int pN, int fId) {
        if(pN==-1){
            oField f = getFieldFromId(fId);
            int nPlots = f.plots.size();
            for(int i=-1; i<nPlots; i++) {
                Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
                int n = 0;
                while (iteratorILC.hasNext()) {
                    oInputLogCalendar ilC = iteratorILC.next();
                    if (ilC.cropId == id && ilC.plotN == i && ilC.fieldId == fId) {
                        inputLogCalendar.remove(n);
                        break;
                    }
                    n++;
                }
            }
        } else {
            Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
            int n = 0;
            while (iteratorILC.hasNext()) {
                oInputLogCalendar ilC = iteratorILC.next();
                if (ilC.cropId == id && ilC.plotN == pN && ilC.fieldId == fId) {
                    inputLogCalendar.remove(n);
                    break;
                }
                n++;
            }
        }
    }

    public void deleteTreatmentFromCalendar(int id, int pN, int fId) {
        if(pN==-1){
            oField f = getFieldFromId(fId);
            int nPlots = f.plots.size();
            for(int i=-1; i<nPlots; i++) {
                Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
                int n = 0;
                while (iteratorILC.hasNext()) {
                    oInputLogCalendar ilC = iteratorILC.next();
                    if (ilC.treatmentId == id && ilC.plotN == i && ilC.fieldId == fId) {
                        inputLogCalendar.remove(n);
                        break;
                    }
                    n++;
                }
            }
        } else {
            Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
            int n = 0;
            while (iteratorILC.hasNext()) {
                oInputLogCalendar ilC = iteratorILC.next();
                if (ilC.treatmentId == id && ilC.plotN == pN && ilC.fieldId == fId) {
                    inputLogCalendar.remove(n);
                    break;
                }
                n++;
            }
        }
    }

    public void deleteActivityFromCalendar(int id, int pN, int fId) {
        if(pN==-1){
            oField f = getFieldFromId(fId);
            int nPlots = f.plots.size();
            for(int i=-1; i<nPlots; i++) {
                Iterator<oActivityCalendar> iteratorAC = activitiesCalendar.iterator();
                int n = 0;
                while (iteratorAC.hasNext()) {
                    oActivityCalendar aC = iteratorAC.next();
                    if (aC.activityId == id && aC.plotN == i && aC.fieldId == fId) {
                        activitiesCalendar.remove(n);
                        break;
                    }
                    n++;
                }
            }
        } else {
            Iterator<oActivityCalendar> iteratorAC = activitiesCalendar.iterator();
            int n = 0;
            while (iteratorAC.hasNext()) {
                oActivityCalendar aC = iteratorAC.next();
                if (aC.activityId == id && aC.plotN == pN && aC.fieldId == fId) {
                    activitiesCalendar.remove(n);
                    break;
                }
                n++;
            }
        }
    }

    public void deleteMeasurementFromCalendar(int id, int pN, int fId) {
        if(pN==-1){
            oField f = getFieldFromId(fId);
            int nPlots = f.plots.size();
            for(int i=-1; i<nPlots; i++) {
                Iterator<oMeasurementCalendar> iteratorM = measurementsCalendar.iterator();
                int n = 0;
                while (iteratorM.hasNext()) {
                    oMeasurementCalendar mC = iteratorM.next();
                    if (mC.measurementId == id && mC.plotN == i && mC.fieldId == fId) {
                        measurementsCalendar.remove(n);
                        break;
                    }
                    n++;
                }
            }
        } else {
            Iterator<oMeasurementCalendar> iteratorM = measurementsCalendar.iterator();
            int n = 0;
            while (iteratorM.hasNext()) {
                oMeasurementCalendar mC = iteratorM.next();
                if (mC.measurementId == id && mC.plotN == pN && mC.fieldId == fId) {
                    measurementsCalendar.remove(n);
                    break;
                }
                n++;
            }
        }
    }

    public void updateCropInputDaysAgo(int id, int pN, int fId, String d){
        if(pN==-1){
            oField f = getFieldFromId(fId);
            int nPlots = f.plots.size();
            for(int i=-1; i<nPlots; i++){
                boolean ciFound=false;
                Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
                while (iteratorILC.hasNext()) {
                    oInputLogCalendar ilC = iteratorILC.next();
                    if(ilC.fieldId==fId && ilC.plotN==i && ilC.cropId==id){
                        ilC.date=d;
                        ciFound=true;
                        break;
                    }
                }
                if(!ciFound){
                    oInputLogCalendar ilC = new oInputLogCalendar();
                    ilC.cropId = id;
                    ilC.plotN = i;
                    ilC.fieldId = fId;
                    ilC.date = d;
                    inputLogCalendar.add(ilC);
                }
            }
        } else {
            boolean ciFound = false;
            Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
            while (iteratorILC.hasNext()) {
                oInputLogCalendar ilC = iteratorILC.next();
                if (id == ilC.cropId && pN == ilC.plotN && fId == ilC.fieldId) {
                    ciFound = true;
                    ilC.date = d;
                    break;
                }
            }
            if (!ciFound) {
                oInputLogCalendar ilC = new oInputLogCalendar();
                ilC.cropId = id;
                ilC.plotN = pN;
                ilC.fieldId = fId;
                ilC.date = d;
                inputLogCalendar.add(ilC);
            }
        }
        writeInputLogCalendarFile();
    }

    public void updateTreatmentInputDaysAgo(int id, int pN, int fId, String d){
        if(pN==-1){
            oField f = getFieldFromId(fId);
            int nPlots = f.plots.size();
            for(int i=-1; i<nPlots; i++){
                boolean tiFound=false;
                Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
                while (iteratorILC.hasNext()) {
                    oInputLogCalendar ilC = iteratorILC.next();
                    if(ilC.fieldId==fId && ilC.plotN==i && ilC.treatmentId==id){
                        ilC.date=d;
                        tiFound=true;
                        break;
                    }
                }
                if(!tiFound){
                    oInputLogCalendar ilC = new oInputLogCalendar();
                    ilC.treatmentId = id;
                    ilC.plotN = i;
                    ilC.fieldId = fId;
                    ilC.date = d;
                    inputLogCalendar.add(ilC);
                }
            }
        } else {
            boolean tiFound = false;
            Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
            while (iteratorILC.hasNext()) {
                oInputLogCalendar ilC = iteratorILC.next();
                if (id == ilC.treatmentId && pN == ilC.plotN && fId == ilC.fieldId) {
                    tiFound = true;
                    ilC.date = d;
                    break;
                }
            }
            if (!tiFound) {
                oInputLogCalendar ilC = new oInputLogCalendar();
                ilC.treatmentId = id;
                ilC.plotN = pN;
                ilC.fieldId = fId;
                ilC.date = d;
                inputLogCalendar.add(ilC);
            }
        }
        writeInputLogCalendarFile();
    }

    public void updateActivityDaysAgo(int id, int pN, int fId, String d){
        if(pN==-1){
            oField f = getFieldFromId(fId);
            int nPlots = f.plots.size();
            for(int i=-1; i<nPlots; i++){
                boolean acFound=false;
                Iterator<oActivityCalendar> iteratorAC = activitiesCalendar.iterator();
                while (iteratorAC.hasNext()) {
                    oActivityCalendar aC = iteratorAC.next();
                    if(aC.fieldId==fId && aC.plotN==i && aC.activityId==id){
                        aC.date=d;
                        acFound=true;
                        break;
                    }
                }
                if(!acFound){
                    oActivityCalendar aC = new oActivityCalendar();
                    aC.activityId = id;
                    aC.plotN = i;
                    aC.fieldId = fId;
                    aC.date = d;
                    activitiesCalendar.add(aC);
                }
            }
        } else {
            boolean acFound = false;
            Iterator<oActivityCalendar> iteratorAC = activitiesCalendar.iterator();
            while (iteratorAC.hasNext()) {
                oActivityCalendar aC = iteratorAC.next();
                if (id == aC.activityId && pN == aC.plotN && fId == aC.fieldId) {
                    acFound = true;
                    aC.date = d;
                    break;
                }
            }
            if (!acFound) {
                oActivityCalendar aC = new oActivityCalendar();
                aC.activityId = id;
                aC.plotN = pN;
                aC.fieldId = fId;
                aC.date = d;
                activitiesCalendar.add(aC);
            }
        }
        writeActivitiesCalendarFile();
    }

    public void updateMeasurementDaysAgo(int id, int pN, int fId, String d){
        if(pN==-1){
            oField f = getFieldFromId(fId);
            int nPlots = f.plots.size();
            for(int i=-1; i<nPlots; i++){
                boolean mFound=false;
                Iterator<oMeasurementCalendar> iteratorM = measurementsCalendar.iterator();
                while (iteratorM.hasNext()) {
                    oMeasurementCalendar mC = iteratorM.next();
                    if(mC.fieldId==fId && mC.plotN==i && mC.measurementId==id){
                        mC.date=d;
                        mFound=true;
                        break;
                    }
                }
                if(!mFound){
                    oMeasurementCalendar mC = new oMeasurementCalendar();
                    mC.measurementId = id;
                    mC.plotN = i;
                    mC.fieldId = fId;
                    mC.date = d;
                    measurementsCalendar.add(mC);
                }
            }
        } else {
            boolean mFound = false;
            Iterator<oMeasurementCalendar> iteratorM = measurementsCalendar.iterator();
            while (iteratorM.hasNext()) {
                oMeasurementCalendar mC = iteratorM.next();
                if (id == mC.measurementId && pN == mC.plotN && fId == mC.fieldId) {
                    mFound = true;
                    mC.date = d;
                    break;
                }
            }
            if (!mFound) {
                oMeasurementCalendar mC = new oMeasurementCalendar();
                mC.measurementId = id;
                mC.plotN = pN;
                mC.fieldId = fId;
                mC.date = d;
                measurementsCalendar.add(mC);
            }
        }
        writeMeasurementsCalendarFile();
    }

    public String getActivityDaysAgo(int activityId, int plotN, int fieldId){
        String ret="-1";
        Iterator<oActivityCalendar> iteratorAC = activitiesCalendar.iterator();
        while (iteratorAC.hasNext()) {
            oActivityCalendar aC = iteratorAC.next();
            if(activityId==aC.activityId && plotN==aC.plotN && fieldId==aC.fieldId){
                int daysAgo = getDaysAgo(stringToDate(aC.date));
                if(daysAgo>15){
                    ret=aC.date;
                } else {
                    ret=Integer.toString(daysAgo);
                }
                break;
            }
        }
        return ret;
    }

    public String getCropInputDaysAgo(int cropId, int plotN, int fieldId){
        String ret="-1";
        Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
        while (iteratorILC.hasNext()) {
            oInputLogCalendar ilC = iteratorILC.next();
            if(cropId==ilC.cropId && plotN==ilC.plotN && fieldId==ilC.fieldId){
                int daysAgo = getDaysAgo(stringToDate(ilC.date));
                if(daysAgo>15){
                    ret=ilC.date;
                } else {
                    ret=Integer.toString(daysAgo);
                }
                break;
            }
        }
        return ret;
    }

    public String getTreatmentInputDaysAgo(int treatmentId, int plotN, int fieldId){
        String ret="-1";
        Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
        while (iteratorILC.hasNext()) {
            oInputLogCalendar ilC = iteratorILC.next();
            if(treatmentId==ilC.treatmentId && plotN==ilC.plotN && fieldId==ilC.fieldId){
                int daysAgo = getDaysAgo(stringToDate(ilC.date));
                if(daysAgo>15){
                    ret=ilC.date;
                } else {
                    ret=Integer.toString(daysAgo);
                }
                break;
            }
        }
        return ret;
    }

    public String getMeasurementDaysAgo(int measurementId, int plotN, int fieldId){
        String ret="-1";
        Iterator<oMeasurementCalendar> iteratorMC = measurementsCalendar.iterator();
        while (iteratorMC.hasNext()) {
            oMeasurementCalendar mC = iteratorMC.next();
            if(measurementId==mC.measurementId && plotN==mC.plotN && fieldId==mC.fieldId){
                int daysAgo = getDaysAgo(stringToDate(mC.date));
                if(daysAgo>15){
                    ret=mC.date;
                } else {
                    ret=Integer.toString(daysAgo);
                }
                break;
            }
        }
        return ret;
    }

    public ArrayList<String> getMeasurementCategories(int userRole){
        ArrayList<String> ret = new ArrayList<>();
        Iterator<oMeasurement> iterator = measurements.iterator();
        while(iterator.hasNext()){
            oMeasurement m = iterator.next();
            if(!ret.contains(m.measurementCategory) && (userRole>0 || (m.measurementIsCommon))){
                ret.add(m.measurementCategory);
            }
        }
        return ret;
    }

    public String getActivityNameFromId(int id){
        String ret="";
        Iterator<oActivity> iterator = activities.iterator();
        while(iterator.hasNext()){
            oActivity a = iterator.next();
            if(a.activityId==id){
                ret=a.activityName;
                break;
            }
        }
        return ret;
    }

    public String getCropNameFromId(int id){
        String ret="";
        Iterator<oCrop> iterator = crops.iterator();
        while(iterator.hasNext()){
            oCrop c = iterator.next();
            if(c.cropId==id){
                ret=c.cropName;
                break;
            }
        }
        return ret;
    }

    public String getTreatmentNameFromId(int id){
        String ret="";
        Iterator<oTreatment> iterator = treatments.iterator();
        while(iterator.hasNext()){
            oTreatment t = iterator.next();
            if(t.treatmentId==id){
                ret=t.treatmentName;
                break;
            }
        }
        return ret;
    }

    public oMeasurement getMeasurementFromId(int id){
        oMeasurement ret = new oMeasurement();
        Iterator<oMeasurement> iterator = measurements.iterator();
        while(iterator.hasNext()){
            oMeasurement m = iterator.next();
            if(m.measurementId==id){
                ret=m;
                break;
            }
        }
        return ret;
    }

    public String getMeasurementNameFromId(int id){
        String ret="";
        Iterator<oMeasurement> iterator = measurements.iterator();
        while(iterator.hasNext()){
            oMeasurement m = iterator.next();
            if(m.measurementId==id){
                ret=m.measurementName;
                break;
            }
        }
        return ret;
    }

    public String getMeasurementDescriptionFromId(int id){
        String ret="";
        Iterator<oMeasurement> iterator = measurements.iterator();
        while(iterator.hasNext()){
            oMeasurement m = iterator.next();
            if(m.measurementId==id){
                ret=m.measurementDescription;
                break;
            }
        }
        return ret;
    }

    public String getMeasurementCategoryFromId(int id){
        String ret="";
        Iterator<oMeasurement> iterator = measurements.iterator();
        while(iterator.hasNext()){
            oMeasurement m = iterator.next();
            if(m.measurementId==id){
                ret=m.measurementCategory;
                break;
            }
        }
        return ret;
    }

    public String getActivityMeasurementUnitsFromId(int id){
        String ret="";
        Iterator<oActivity> iterator = activities.iterator();
        while(iterator.hasNext()){
            oActivity a = iterator.next();
            if(a.activityId==id){
                ret=a.activityMeasurementUnits;
                break;
            }
        }
        return ret;
    }

    public ArrayList<oCrop> getAllCrops(){
        ArrayList<oCrop> ret = new ArrayList<>();
        ret=crops;
        Collections.sort(ret, new Comparator<oCrop>() {
            @Override
            public int compare(oCrop c1, oCrop c2) {
                return c1.cropName.compareTo(c2.cropName);
            }
        });
        return ret;
    }

    public ArrayList<oCrop> getCropsFromFieldId(int id){
        ArrayList<oCrop> ret = new ArrayList<>();
        oField f = getFieldFromId(id);
        Iterator<oPlot> iterator = f.plots.iterator();
        while(iterator.hasNext()){
            oPlot p = iterator.next();
            oCrop pc = p.primaryCrop;
            if(!ret.contains(pc)){
                ret.add(pc);
            }
            if(p.intercroppingCrop!=null) {
                oCrop ic = p.intercroppingCrop;
                if (!ret.contains(ic)) {
                    ret.add(ic);
                }
            }
        }
        Collections.sort(ret, new Comparator<oCrop>() {
            @Override
            public int compare(oCrop c1, oCrop c2) {
                return c1.cropName.compareTo(c2.cropName);
            }
        });
        return ret;
    }

    public String getPlotNames(oField f, String p){
        String ret="";
        String[] plots = p.split(",");
        for(int i=0;i<plots.length;i++){
            String cropString="";
            String treatmentString="";
            String plotString;
            oPlot plot = f.plots.get(Integer.valueOf(plots[i]));
            cropString = plot.primaryCrop.cropSymbol;
            if(plot.hasPestControl){
                treatmentString="P";
            }
            if(plot.hasSoilManagement){
                treatmentString+="S";
            }
            if(plot.intercroppingCrop!=null){
                treatmentString+="L";
            }
            if(!treatmentString.isEmpty()){
                plotString=cropString+"-"+treatmentString;
            } else {
                plotString=cropString;
            }
            if(ret.isEmpty()){
                ret=plotString;
            } else {
                ret=ret+", "+plotString;
            }
        }
        return ret;
    }

    public ArrayList<oCrop> getPlotCropsFromFieldId(int id, int pN){
        ArrayList<oCrop> ret = new ArrayList<>();
        oField f = getFieldFromId(id);
        oPlot p = f.plots.get(pN);
        ret.add(p.primaryCrop);
        if(p.intercroppingCrop!=null){
            ret.add(p.intercroppingCrop);
        }
        Collections.sort(ret, new Comparator<oCrop>() {
            @Override
            public int compare(oCrop c1, oCrop c2) {
                return c1.cropName.compareTo(c2.cropName);
            }
        });
        return ret;
    }

    public ArrayList<oTreatment> getInputTreatmentsFromPlotFieldId(int id, int pN){
        ArrayList<oTreatment> ret = new ArrayList<>();
        oField f = getFieldFromId(id);
        oPlot p = f.plots.get(pN);
        if(p.hasPestControl){
            ArrayList<oTreatment> ts = getTreatmentsFromCategory("Pest control");
            if(ts.size()>0) {
                Iterator<oTreatment> iterator = ts.iterator();
                while(iterator.hasNext()){
                    oTreatment t = iterator.next();
                    if(!ret.contains(t)){
                        ret.add(t);
                    }
                }
            }
        }
        if(p.hasSoilManagement){
            ArrayList<oTreatment> ts = getTreatmentsFromCategory("Soil management");
            if(ts.size()>0) {
                Iterator<oTreatment> iterator = ts.iterator();
                while(iterator.hasNext()){
                    oTreatment t = iterator.next();
                    if(!ret.contains(t)){
                        ret.add(t);
                    }
                }
            }
        }
        return ret;
    }

    public ArrayList<oTreatment> getInputTreatmentsFromFieldId(int id){
        ArrayList<oTreatment> ret = new ArrayList<>();
        oField f = getFieldFromId(id);
        if(f.hasPestControl){
            ArrayList<oTreatment> ts = getTreatmentsFromCategory("Pest control");
            if(ts.size()>0) {
                Iterator<oTreatment> iterator = ts.iterator();
                while(iterator.hasNext()){
                    oTreatment t = iterator.next();
                    if(!ret.contains(t)){
                        ret.add(t);
                    }
                }
            }
        }
        if(f.hasSoilManagement){
            ArrayList<oTreatment> ts = getTreatmentsFromCategory("Soil management");
            if(ts.size()>0) {
                Iterator<oTreatment> iterator = ts.iterator();
                while(iterator.hasNext()){
                    oTreatment t = iterator.next();
                    if(!ret.contains(t)){
                        ret.add(t);
                    }
                }
            }
        }
        return ret;
    }

    public ArrayList<oTreatment> getTreatmentsFromCategory(String c){
        ArrayList<oTreatment> ret = new ArrayList<>();
        Iterator<oTreatment> iterator = treatments.iterator();
        while(iterator.hasNext()){
            oTreatment t = iterator.next();
            if(t.treatmentCategory.equals(c)){
                if(!ret.contains(t)){
                    ret.add(t);
                }
            }
        }
        Collections.sort(ret, new Comparator<oTreatment>() {
            @Override
            public int compare(oTreatment t1, oTreatment t2) {
                return t1.treatmentName.compareTo(t2.treatmentName);
            }
        });
        return ret;
    }

    public void writeActivitiesCalendarFile(){
        String data="";
        Iterator<oActivityCalendar> iteratorAC = activitiesCalendar.iterator();
        while (iteratorAC.hasNext()) {
            oActivityCalendar aC = iteratorAC.next();
            data+=Integer.toString(aC.activityId)+","+Integer.toString(aC.plotN)+","+Integer.toString(aC.fieldId)+","+aC.date+";";
        }
        writeToFile(data,"activities_calendar");
    }

    public void writeInputLogCalendarFile(){
        String data="";
        Iterator<oInputLogCalendar> iteratorILC = inputLogCalendar.iterator();
        while (iteratorILC.hasNext()) {
            oInputLogCalendar ilC = iteratorILC.next();
            data+=Integer.toString(ilC.cropId)+","+Integer.toString(ilC.treatmentId)+","+Integer.toString(ilC.plotN)+","+Integer.toString(ilC.fieldId)+","+ilC.date+";";
        }
        writeToFile(data,"input_log_calendar");
    }

    public void writeMeasurementsCalendarFile(){
        String data="";
        Iterator<oMeasurementCalendar> iteratorM = measurementsCalendar.iterator();
        while (iteratorM.hasNext()) {
            oMeasurementCalendar mC = iteratorM.next();
            data+=Integer.toString(mC.measurementId)+","+Integer.toString(mC.plotN)+","+Integer.toString(mC.fieldId)+","+mC.date+";";
        }
        writeToFile(data,"measurements_calendar");
    }

    public List<String[]> readCSVFile(String filename){
        List<String[]> ret = null;

        File file = new File(context.getFilesDir(), filename);
        if(file.exists()) {
            try {
                FileReader r = new FileReader(file);
                CSVReader reader = new CSVReader(r, ',', '"');
                ret = reader.readAll();
            } catch (IOException e) {

            } finally {
                return ret;
            }
        }

        return ret;
    }

    private void writeToFile(String data, String filename) {
        try {
            OutputStreamWriter outputStreamWriter = new OutputStreamWriter(context.openFileOutput(filename, Context.MODE_PRIVATE));
            outputStreamWriter.write(data);
            outputStreamWriter.close();
        }
        catch (IOException e) {

        }
    }

    private String readFromFile(String filename) {
        String ret = "";
        try {
            InputStream inputStream = context.openFileInput(filename);

            if ( inputStream != null ) {
                InputStreamReader inputStreamReader = new InputStreamReader(inputStream);
                BufferedReader bufferedReader = new BufferedReader(inputStreamReader);
                String receiveString = "";
                StringBuilder stringBuilder = new StringBuilder();

                while ( (receiveString = bufferedReader.readLine()) != null ) {
                    stringBuilder.append(receiveString);
                }

                inputStream.close();
                ret = stringBuilder.toString();
            }
        }
        catch (FileNotFoundException e) {

        } catch (IOException e) {

        } finally {
            return ret;
        }
    }

    private void writeLog(){
        String data="";
        Iterator<oLog> iterator = log.iterator();
        while (iterator.hasNext()) {
            oLog l = iterator.next();
            data+=Integer.toString(l.logFieldId)+";"+l.logPlots+";"+Integer.toString(l.logUserId)+";"+Integer.toString(l.logCropId)
                    +";"+Integer.toString(l.logTreatmentId)+";"+Integer.toString(l.logMeasurementId)+";"+Integer.toString(l.logActivityId)
                    +";"+dateToString(l.logDate)+";"+Float.toString(l.logNumberValue)+";"+l.logValueUnits+ ";"+l.logTextValue+";"+l.logLaborers
                    +";"+l.logCost+";"+l.logComments+";"+Integer.toString(l.logId)+";"+Integer.toString(l.logSampleNumber)+"|";
        }
        writeToFile(data,"log");
    }

    private void writeInputLog(){
        String data="";
        Iterator<oInputLog> iterator = inputLog.iterator();
        while (iterator.hasNext()) {
            oInputLog l = iterator.next();
            data+=Integer.toString(l.inputLogFieldId)+";"+l.inputLogPlots+";"
                    +Integer.toString(l.inputLogUserId)+";"+Integer.toString(l.inputLogCropId)+";"+Integer.toString(l.inputLogTreatmentId)
                    +";"+dateToString(l.inputLogDate)+";"+l.inputLogInputAge+";"+l.inputLogInputOrigin+";"+l.inputLogCropVariety+";"
                    +Float.toString(l.inputLogInputQuantity)+";"+l.inputLogInputUnits+";"+l.inputLogInputCost+";"+l.inputLogTreatmentMaterial
                    +";"+l.inputLogTreatmentPreparationMethod+";"+l.inputLogComments+";"+Integer.toString(l.inputLogId)+"|";
        }
        writeToFile(data,"input_log");
    }

}
