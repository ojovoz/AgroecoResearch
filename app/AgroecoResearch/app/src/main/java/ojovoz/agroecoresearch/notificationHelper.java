package ojovoz.agroecoresearch;

import android.content.Context;

import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import au.com.bytecode.opencsv.CSVReader;
import au.com.bytecode.opencsv.CSVWriter;

/**
 * Created by Eugenio on 15/09/2017.
 */
public class notificationHelper {

    private Context context;
    ArrayList<oNotification> notifications;

    notificationHelper(Context context){
        this.context=context;
        createNotifications();
    }

    public void createNotifications(){
        notifications = new ArrayList<>();
        List<String[]> notificationsCSV = readCSVFile("notifications");
        if(notificationsCSV!=null) {
            Iterator<String[]> iterator = notificationsCSV.iterator();
            while (iterator.hasNext()) {
                String[] record = iterator.next();
                oNotification notification = new oNotification();
                notification.notificationId = Integer.parseInt(record[0]);
                notification.forUserId = Integer.parseInt(record[1]);
                notification.notificationSender = record[2];
                notification.notificationDate = record[3];
                notification.notificationText = record[4];
                notifications.add(notification);
            }
        }
    }

    public boolean notificationsPending(int userId){
        boolean ret = false;
        Iterator<oNotification> iterator = notifications.iterator();
        while (iterator.hasNext()) {
            oNotification n = iterator.next();
            if(n.forUserId==userId){
                ret=true;
                break;
            }
        }
        return ret;
    }

    public ArrayList<oNotification> getNotificationsForUserId(int userId){
        ArrayList<oNotification> ret = new ArrayList<>();
        Iterator<oNotification> iterator = notifications.iterator();
        while (iterator.hasNext()) {
            oNotification n = iterator.next();
            if(n.forUserId==userId){
                ret.add(n);
            }
        }
        return ret;
    }

    public void deleteNotification(int id){
        Iterator<oNotification> iterator = notifications.iterator();
        int i=0;
        while (iterator.hasNext()) {
            oNotification n = iterator.next();
            if(n.notificationId==id){
                notifications.remove(i);
                updateNotifications();
                break;
            }
            i++;
        }
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

    public void appendNewNotifications(String data){
        String[] nextLine;
        CSVReader reader = new CSVReader(new StringReader(data),',','"');
        try {
            while ((nextLine = reader.readNext()) != null) {
                oNotification notification = new oNotification();
                notification.notificationId = Integer.parseInt(nextLine[0]);
                notification.forUserId = Integer.parseInt(nextLine[1]);
                notification.notificationSender = nextLine[2];
                notification.notificationDate = nextLine[3];
                notification.notificationText = nextLine[4];
                notifications.add(notification);
            }
            updateNotifications();
        } catch (IOException e){

        }

    }

    public void updateNotifications(){
        String data = "";
        String[] nextLine;
        Iterator<oNotification> iterator = notifications.iterator();
        while (iterator.hasNext()) {
            oNotification n = iterator.next();
            if(data.isEmpty()){
                data=n.notificationId+","+n.forUserId+",\""+n.notificationSender+"\",\""+n.notificationDate+"\",\""+n.notificationText+"\"";
            } else {
                data=data+"\n"+n.notificationId+","+n.forUserId+",\""+n.notificationSender+"\",\""+n.notificationDate+"\",\""+n.notificationText+"\"";
            }
        }
        CSVReader reader = new CSVReader(new StringReader(data),',','"');
        context.deleteFile("notifications");
        File file = new File(context.getFilesDir(), "notifications");
        try {
            FileWriter w = new FileWriter(file);
            CSVWriter writer = new CSVWriter(w, ',', '"');
            while((nextLine = reader.readNext()) != null){
                writer.writeNext(nextLine);
            }
            writer.close();
            reader.close();
        } catch (IOException e) {

        }
    }
}
