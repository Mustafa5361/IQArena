using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class TokenKontrol : MonoBehaviour
{

    private void Awake()
    {
        
        if(FileSystem.GetFile("Token"))
        {
            GameManager.SetToken(FileSystem.JsonLoad<string>("Token"));

            ApiConnection.Connection<LoginGetData>("", new LoginGetData(true, GameManager.token), (value)=>
            {

            });

        }

    }

}
