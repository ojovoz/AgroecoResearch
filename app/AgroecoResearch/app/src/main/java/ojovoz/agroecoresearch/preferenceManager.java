package ojovoz.agroecoresearch;

import android.content.Context;
import android.content.SharedPreferences;

import java.util.ArrayList;

/**
 * Created by Eugenio on 13/03/2017.
 */
public class preferenceManager {

    Context context;

    preferenceManager(Context c){
        context=c;
    }

    public String getPreference(String keyName) {
        String value = "";
        SharedPreferences agroEcoPrefs = context.getSharedPreferences("agroEcoPrefs", Context.MODE_PRIVATE);
        value = agroEcoPrefs.getString(keyName, "");
        return value;
    }

    public boolean exists(String keyName) {
        boolean ret;
        SharedPreferences agroEcoPrefs = context.getSharedPreferences("agroEcoPrefs", Context.MODE_PRIVATE);
        ret = agroEcoPrefs.contains(keyName);
        return ret;
    }

    public void savePreference(String keyName, String keyValue) {
        SharedPreferences agroEcoPrefs = context.getSharedPreferences("agroEcoPrefs", Context.MODE_PRIVATE);
        SharedPreferences.Editor prefEditor = agroEcoPrefs.edit();
        prefEditor.putString(keyName, keyValue);
        prefEditor.commit();
    }

    public void savePreferenceBoolean(String keyName, boolean keyValue){
        SharedPreferences agroEcoPrefs = context.getSharedPreferences("agroEcoPrefs", Context.MODE_PRIVATE);
        SharedPreferences.Editor prefEditor = agroEcoPrefs.edit();
        prefEditor.putBoolean(keyName, keyValue);
        prefEditor.commit();
    }

    public void updateUserPrefs(String keyName, String keyValue){
        String value = getPreference(keyName);
        if(value.isEmpty()){
            savePreference(keyName,keyValue);
        } else if(!value.contains(keyValue)){
            savePreference(keyName,value + ";" + keyValue);
        }
    }

    public String getUserFromPrefs(String keyName, String aliasPass){
        String ret="-1";
        String value = getPreference(keyName);
        CharSequence users[] = value.split(";");
        for(int i=0; i < users.length; i++){
            if(users[i].toString().contains(aliasPass)){
                CharSequence parts[] = users[i].toString().split(",");
                ret=parts[2]+","+parts[3];
                break;
            }
        }
        return ret;
    }

    public String getUserNames(String keyName){
        String ret = "";
        String value = getPreference(keyName);
        String users[] = value.split(";");
        for(int i=0; i < users.length; i++) {
            String parts[] = users[i].split(",");
            if(ret.isEmpty()){
                ret=parts[0];
            } else {
                ret=ret+","+parts[0];
            }
        }
        return ret;
    }

    public ArrayList<String> getArrayListPreference(String keyName){
        ArrayList<String> ret = new ArrayList<>();
        String allValues=getPreference(keyName);
        if(!allValues.isEmpty()) {
            String valuesArray[] = allValues.split("\\*");
            if(valuesArray.length>0){
                for(int i=0;i<valuesArray.length;i++){
                    ret.add(valuesArray[i]);
                }
            } else {
                ret.add(allValues);
            }
        }
        return ret;
    }

    public void appendIfNotExists(String keyName, String value){
        String previousValue = getPreference(keyName);
        if(!previousValue.isEmpty()) {
            String previousValues[] = previousValue.split("\\*");
            if(previousValues.length>0){
                boolean bFound=false;
                for(int i=0;i<previousValues.length;i++){
                    if(previousValues[i].equals(value)){
                        bFound=true;
                        break;
                    }
                }
                if(!bFound){
                    String newValue = previousValue+"*"+value;
                    savePreference(keyName,newValue);
                }
            } else {
                savePreference(keyName, value);
            }
        } else {
            savePreference(keyName, value);
        }
    }
}
