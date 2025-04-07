using System;
using System.Threading.Tasks;
using UnityEngine;
using UnityEngine.Networking;

public static class ApiConnection
{

    private static string URL = "localhost/";


    public static async void Connection<T>(string connection,T value, Action<T> GetData)
    {

        Connection<T, T>(connection, value, (Getvalue) =>
        {
            GetData.Invoke(Getvalue);
        });

    }

    public static async void Connection<T1,T2>(string connection,T1 value, Action<T2> GetData)
    {

        WWWForm data = new WWWForm();
        data.AddField("value", JsonUtility.ToJson(value));

        using (UnityWebRequest request = UnityWebRequest.Post(URL + connection, data))
        {

            var control = request.SendWebRequest();

            while (!control.isDone)
                Task.Delay(10);

            if (request.result == UnityWebRequest.Result.Success)
            {

                var result = JsonUtility.FromJson<T2>(request.downloadHandler.text);
                GetData.Invoke(result);

            }
            else
                Debug.Log("ERROR : " + request.error);


        }

    }

}
