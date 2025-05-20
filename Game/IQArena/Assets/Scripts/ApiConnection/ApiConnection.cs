using System;
using System.Reflection;
using System.Threading.Tasks;
using UnityEngine;
using UnityEngine.Networking;

public static class ApiConnection
{

    private static string URL = "https://penguen.net/iqarena/";

    public static async void Connection<T>(string connection, T value, Action<T> GetData)
    {
        Connection<T, T>(connection, value, GetData);
    }

    public static async void Connection<T1,T2>(string connection,T1 value, Action<T2> GetData)
    {

        WWWForm data = new WWWForm();
        data.AddField("value", JsonUtility.ToJson(value));

        using (UnityWebRequest request = UnityWebRequest.Post(URL + connection, data))
        {

            var control = request.SendWebRequest();

            while (!control.isDone)
                await Task.Delay(10);

            if (request.result == UnityWebRequest.Result.Success)
            {

                Debug.Log(request.downloadHandler.text);
                T2 returnValue = JsonUtility.FromJson<T2>(request.downloadHandler.text);
                GetData.Invoke(returnValue);

            }
            else
                Debug.Log("ERROR : " + request.error);


        }

    }

    public static async void Connection()
    {

        UnityWebRequest request = UnityWebRequest.Get(URL + "index.php");
        {

            var control = request.SendWebRequest();

            while (!control.isDone)
               await Task.Delay(10);

            if (request.result == UnityWebRequest.Result.Success)
            {

                Debug.Log(request.downloadHandler.text);

            }
            else
            {
                Debug.Log("ERROR : " + request.error);
            }

        }

    }

}
