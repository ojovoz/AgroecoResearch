package ojovoz.agroecoresearch;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.View;
import android.widget.CheckBox;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.Iterator;

/**
 * Created by Eugenio on 18/09/2017.
 */
public class notifications extends AppCompatActivity {

    public int userId;
    public int userRole;

    public notificationHelper nHelper;
    public ArrayList<oNotification> displayedNotifications;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notifications);

        userId = getIntent().getExtras().getInt("userId");
        userRole = getIntent().getExtras().getInt("userRole");

        nHelper = new notificationHelper(this);

        fillTable();
    }

    /*
    @Override public void onResume() {
        super.onResume();
        if(userId==0){
            final Context context = this;
            Intent i;
            i = new Intent(context, loginScreen.class);
            startActivity(i);
            finish();
            return;
        }
    }
    */

    @Override public void onBackPressed(){
        final Context context = this;
        Intent i = new Intent(context, mainMenu.class);
        i.putExtra("userId",userId);
        i.putExtra("userRole",userRole);
        startActivity(i);
        finish();
    }

    public void fillTable(){
        displayedNotifications = nHelper.getNotificationsForUserId(userId);

        TableLayout notificationsTable = (TableLayout) findViewById(R.id.notificationsTable);
        notificationsTable.removeAllViews();

        int n=0;

        if(displayedNotifications.size()>0){
            Iterator<oNotification> notificationsIterator = displayedNotifications.iterator();
            while (notificationsIterator.hasNext()) {
                oNotification notification = notificationsIterator.next();

                final TableRow trow = new TableRow(notifications.this);
                TableRow.LayoutParams lp = new TableRow.LayoutParams(TableRow.LayoutParams.MATCH_PARENT, TableRow.LayoutParams.MATCH_PARENT, 1.0f);
                lp.setMargins(10, 10, 0, 10);

                if (n % 2 == 0) {
                    trow.setBackgroundColor(ContextCompat.getColor(this, R.color.lightGray));
                } else {
                    trow.setBackgroundColor(ContextCompat.getColor(this, R.color.colorWhite));
                }

                CheckBox cb = new CheckBox(notifications.this);
                cb.setButtonDrawable(R.drawable.delete_checkbox);
                cb.setId(notification.notificationId);
                cb.setPadding(4, 4, 4, 4);
                cb.setChecked(false);
                cb.setOnClickListener(new View.OnClickListener(){
                    @Override
                    public void onClick(View v){
                        deleteNotification(v);
                    }
                });
                trow.addView(cb, lp);

                TextView tv = new TextView(notifications.this);
                tv.setId(notification.notificationId);
                tv.setTextColor(ContextCompat.getColor(this, R.color.colorPrimary));
                tv.setTextSize(TypedValue.COMPLEX_UNIT_DIP, 17f);
                tv.setText("From: "+notification.notificationSender+"\nDate: "+notification.notificationDate+"\n"+notification.notificationText);
                tv.setTextAlignment(View.TEXT_ALIGNMENT_TEXT_START);
                tv.setPadding(0, 10, 0, 10);
                trow.addView(tv, lp);

                trow.setGravity(Gravity.CENTER_VERTICAL);
                notificationsTable.addView(trow, lp);

                n++;
            }
        }
    }

    public void deleteNotification(View v){
        final CheckBox c = (CheckBox)v;
        c.setChecked(true);
        final int deleteId = c.getId();

        String msg = this.getResources().getString(R.string.deleteNotificationMessage);

        AlertDialog.Builder logoutDialog = new AlertDialog.Builder(this);
        logoutDialog.setTitle(R.string.deleteNotification);
        logoutDialog.setMessage(msg);
        logoutDialog.setNegativeButton(R.string.cancelButtonText, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                c.setChecked(false);
            }
        });
        logoutDialog.setPositiveButton(R.string.okButtonText, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                c.setChecked(false);
                doDelete(deleteId);
            }
        });
        logoutDialog.create();
        logoutDialog.show();
    }

    public void doDelete(int id){
        nHelper.deleteNotification(id);
        if(nHelper.notificationsPending(userId)){
            fillTable();
        } else {
            final Context context = this;
            Intent i = new Intent(context, mainMenu.class);
            i.putExtra("userId",userId);
            i.putExtra("userRole",userRole);
            startActivity(i);
            finish();
        }
    }
}
