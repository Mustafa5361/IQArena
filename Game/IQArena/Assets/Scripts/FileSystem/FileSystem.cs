using System.Collections.Generic;
using System.IO;
using UnityEngine;

public static class FileSystem
{

    private static bool FileControl(string value)
    {
        string folderPath = Application.dataPath + "/Saves";

        if (!Directory.Exists(folderPath))
        {
            Directory.CreateDirectory(folderPath);
            return true;
        }
        else
        {
            return false;
        }
    }

    public static void JsonSave(string name, object Value)
    {

        string folderPath = Application.dataPath + "/Saves/";

        if (FileControl(folderPath))
        {
            string jsonFormat = JsonUtility.ToJson(Value, true);
            File.WriteAllText(folderPath + name + ".json", jsonFormat);
            return;
        }

        return;
        throw new System.Exception("File oluþturulamadý.");

    }

    public static void JsonSave<T>(string name, List<T> Value)
    {

        SaveList<T> saveList = new SaveList<T>();
        saveList.saveList = Value;
        string jsonFormat = JsonUtility.ToJson(saveList, true);
        File.WriteAllText(Application.dataPath + "/Saves/" + name + ".json", jsonFormat);

    }

    public static T JsonLoad<T>(string name)
    {

        string valueStr = File.ReadAllText(Application.dataPath + "/Saves/" + name + ".json");
        return JsonUtility.FromJson<T>(valueStr);

    }

    public static List<T> JsonLoadToList<T>(string name)
    {

        SaveList<T> saveList = new SaveList<T>();

        string Value = File.ReadAllText(Application.dataPath + "/Saves/" + name + ".json");
        saveList = JsonUtility.FromJson<SaveList<T>>(Value);

        return saveList.saveList;

    }

    public static string[] GetFiles()
    {

        string[] pages = Directory.GetFiles(Application.dataPath + "/Saves/", "*.json");

        //parça yollatýný isimlerle deðiþmek iþin düzenleme

        if (pages.Length != 0)
        {

            for (int i = 0; i < pages.Length; i++)
            {

                string[] parca = pages[i].Split("/");

                pages[i] = parca[parca.Length - 1].Split(".")[0];

            }

        }

        return pages;

    }

    public static bool GetFile(string name)
    {

        if (Directory.GetFiles(Application.dataPath + "/Saves/" + name + ".json").Length > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private class SaveList<T>
    {
        public List<T> saveList = new List<T>();
    }
}
