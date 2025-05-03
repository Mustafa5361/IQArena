using System.Collections;
using System.Collections.Generic;
using Unity.VisualScripting;
using UnityEngine;
using UnityEngine.UI;

public class GameManager : MonoBehaviour
{

    public static string token { get; private set; }

    public static void SetToken(string token)
    {
        GameManager.token = token;
    }

    private void Awake()
    {

        DontDestroyOnLoad(this);
    }

}
