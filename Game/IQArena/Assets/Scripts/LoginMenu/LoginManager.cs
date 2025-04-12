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
  


    [SerializeField] private Text usernameLogin;
    [SerializeField] private Text passwordLogin;

    [SerializeField] private Text mailSignIn;
    [SerializeField] private Text usernameSignIn;
    [SerializeField] private Text passwordSignIn;
    [SerializeField] private Text passwordControlSignIn;

    [SerializeField] private Text ActivationCodeTxt;

    public void Login()
    {

        if (usernameLogin.text != "" && passwordLogin.text != "")
        {

            ApiConnection.Connection<User, LoginSetData>("login.php", new User("", usernameLogin.text, passwordLogin.text), (value) =>
            {

                if (value.success)
                {
                    if (true) // hesabý kaydet açýkmý
                    {

                        FileSystem.JsonSave("Token", value.token);

                    }

                    GameManager.SetToken(value.token);

                    Debug.Log("giriþ Baþarýlý.");
                }
                else
                    Debug.Log("giriþ Hatalý.");

            });

        }
        else
        {
            Debug.Log("Giriþ Bilgilerini doldurunuz.");
        }


    }

    public void SignIn()
    {
        if (usernameSignIn.text != "" && passwordSignIn.text != "" && passwordControlSignIn.text != "" && mailSignIn.text != "")
            if (mailSignIn.text.Contains("@"))
                if (passwordSignIn.text == passwordControlSignIn.text)
                {

                    ApiConnection.Connection<User, LoginSetData>("login.php", new User(mailSignIn.text, usernameSignIn.text, passwordSignIn.text), (value) =>
                    {

                        if (value.success)
                        {
                            GameManager.SetToken(value.token);
                            signinPanel.SetActive(false);
                            activationControlPanel.SetActive(true);
                        }
                        else
                        {
                            Debug.Log("ERROR : " + value.message);
                        }

                    });

                }
                else
                    Debug.Log("Þifreleri Ayný Girin.");
            else
                Debug.Log("Düzgün bir mail adresi giriniz.");
        else
            Debug.Log("Tüm Alanlarý Doldurun.");

    }

    public void sendActivationCode()
    {

        if (ActivationCodeTxt.text.Trim().Length == 6)
        {

            ApiConnection.Connection<SendToActivalionCode, LoginSetData>("login.php", new SendToActivalionCode(GameManager.token,ActivationCodeTxt.text.Trim(), false), (value) =>
            {

                if (value.success)
                {
                    GameManager.SetToken(value.token);
                    //oyun ekraný yüklenicek.
                }
                else
                    Debug.Log("Aktivation Code hatalý");

            });

        }
        else
            Debug.Log("Aktivasyon kodu 6 haneli olmalý");

    }

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

    public void LogingeriPanelOpen()
    {
        signinPanel.SetActive(false );
        passwordConfirmationPanel.SetActive(false);
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
