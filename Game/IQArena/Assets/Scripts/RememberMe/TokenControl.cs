using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;

public class TokenControl : MonoBehaviour
{

    private void Awake()
    {
        
        if(FileSystem.GetFile("Token"))
        {
            GameManager.SetToken(FileSystem.JsonLoad<SaveToken>("Token").token);

            ApiConnection.Connection<SetTokenControl, LoginSetData>("login.php", new SetTokenControl(GameManager.Token, false), (value)=>
            {

                if (value.success)
                {

                    GameManager.SetToken(value.token);
                    SceneManager.LoadScene("MainMenu");


                }
                else
                {
                    Debug.Log(value.message);
                    SceneManager.LoadScene("Login");
                }

            });

        }
        else
            SceneManager.LoadScene("Login");
    }

}

public class SetTokenControl
{

    public string token;
    public bool logOut;

    public SetTokenControl() { }

    public SetTokenControl(string token, bool logOut)
    {

        this.token = token;
        this.logOut = logOut;

    }
    
}