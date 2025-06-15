using System.Collections;
using System.Collections.Generic;
using Unity.VisualScripting;
using UnityEngine;
using UnityEngine.UI;

public class GameManager : MonoBehaviour
{

    private static int timer = 0;

    public static int Timer 
    {  
        get 
        {
            return timer; 
        } 
    }

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
        StartCoroutine(TimerFlow());


    }

    IEnumerator TimerFlow()
    {

        while (timer > 0)
        {

            yield return new WaitForSeconds(1f);

            timer++;
        
        }
    }

}
