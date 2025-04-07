using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

public class LoginManager : MonoBehaviour
{

    [SerializeField] private GameObject menuPenel;
    [SerializeField] private GameObject loginPanel;
    [SerializeField] private GameObject signinPanel;
    [SerializeField] private GameObject passwordResetPanel;
    [SerializeField] private GameObject passwordConfirmationPanel;
    [SerializeField] private GameObject activationControlPanel;
    [SerializeField] private GameObject logingeriPanel;

<<<<<<< Updated upstream
    
=======
    [SerializeField] private Text kAdiLogin;
    [SerializeField] private Text passwordLogin;

    [SerializeField] private Text mailSignIn;
    [SerializeField] private Text kAdiSignIn;
    [SerializeField] private Text passwordSignIn;

    public void Login()
    {

        ApiConnection.Connection<User, LoginGetData>("Login.php", new User("", kAdiLogin.text, passwordLogin.text), (value) =>
        {

            if (value.success)
            {
                Debug.Log("giriþ Baþarýlý.");
            }
            else
                Debug.Log("giriþ Hatalý.");

        });

    }

    public void SignIn()
    {

        ApiConnection.Connection<User, LoginGetData>("Login.php", new User(mailSignIn.text, kAdiSignIn.text, passwordSignIn.text),(value) =>
        {

            if (value.success)
            {
                Debug.Log("Kayýt Baþarýlý");
            }
            else
            {
                Debug.Log("Kayýt Hatalý");
            }

        });

    }
>>>>>>> Stashed changes

    public void LoginPanelOpen()
    {

        menuPenel.SetActive(false);
        loginPanel.SetActive(true);

    }

    public void SigninPanelOpen()
    {
        menuPenel.SetActive(false);
        signinPanel.SetActive(true);

    }

    public void PasswordResetPanelOpen()
    {
        
        loginPanel.SetActive(false);
        passwordResetPanel.SetActive(true);
    
   
    }

    public void ActivationControlPanelOpen()
    {

        passwordResetPanel.SetActive(false);
        activationControlPanel.SetActive(true);

    }

    public void PasswordConfirmationPanelOpen()
    {

        activationControlPanel.SetActive(false);
        passwordConfirmationPanel.SetActive(true);
    
    }

    public void LogingeriPanelOpen()
    {
        
        loginPanel.SetActive(false) ;
        menuPenel .SetActive(true) ;
    }

    

   

    void Start()
    {
        menuPenel.SetActive(true);
    }

    void Update()
    {
        
    }
}
