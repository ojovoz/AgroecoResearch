package ojovoz.agroecoresearch;

import java.io.File;

/**
 * Created by Eugenio on 13/03/2017.
 */
public class fileHandler {

    public File folder;

    fileHandler(){

    }

    public void createDir(String dir) {
        folder = new File(dir);
        if (!folder.exists()) {
            folder.mkdirs();
        }
    }
}
