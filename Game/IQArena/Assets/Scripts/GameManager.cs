using System.Collections;
using System.Collections.Generic;
using Unity.VisualScripting;
using UnityEngine;
using UnityEngine.UI;

public class GameManager : MonoBehaviour
{

    private static string token;

    public static string Token
    {
        get 
        {
            return token; 
        }
    }

    public static void SetToken(string token)
    {
        GameManager.token = token;
    }

    private void Awake()
    {

        DontDestroyOnLoad(this);
    }

}
