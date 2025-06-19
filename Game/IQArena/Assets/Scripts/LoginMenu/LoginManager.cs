using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;
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
    [SerializeField] private GameObject quitMenu;

    [SerializeField] private Text usernameLogin;
    [SerializeField] private InputField passwordLogin;

    [SerializeField] private Text mailSignIn;
    [SerializeField] private Text usernameSignIn;
    [SerializeField] private InputField passwordSignIn;
    [SerializeField] private InputField passwordControlSignIn;
    [SerializeField] private InputField passwordConfirmationInput;
    [SerializeField] private InputField passworCondirmationnRepead;
    [SerializeField] private InputField passwordResetMail;

    [SerializeField] private Text ActivationCodeTxt;

    [SerializeField] private Toggle activationControlToggle;

    private string thisMail;
    private string thisActivationCode;

    private bool isPasswordVisible = false;
    private bool isPasswordSignInVisible = false;
    private bool isPasswordControlVisible = false;
    private bool isPasswordConfirmationVisible = false;
    private bool isPassworCondirmationnVisible = false;

    public void TogglePasswordVisibility()
    {
        isPasswordVisible = !isPasswordVisible;

        if (isPasswordVisible)
            passwordLogin.contentType = InputField.ContentType.Standard;
        else
            passwordLogin.contentType = InputField.ContentType.Password;

        passwordLogin.ForceLabelUpdate();
    }
    public void TogglePasswordSignInVisibility()
    {
        isPasswordSignInVisible = !isPasswordSignInVisible;
        isPasswordControlVisible = !isPasswordControlVisible;

        if (isPasswordSignInVisible  && isPasswordControlVisible)
        {
            passwordSignIn.contentType = InputField.ContentType.Standard;
            passwordControlSignIn.contentType = InputField.ContentType.Standard;
        }
        else
        {
            passwordSignIn.contentType = InputField.ContentType.Password;
            passwordControlSignIn.contentType = InputField.ContentType.Password;
        }
        passwordSignIn.ForceLabelUpdate();
        passwordControlSignIn.ForceLabelUpdate();
    }
   
    public void TogglePasswordConfirmationVisibility()
    {
        isPasswordConfirmationVisible = !isPasswordConfirmationVisible;
        isPassworCondirmationnVisible = !isPassworCondirmationnVisible;

        if (isPasswordConfirmationVisible && isPassworCondirmationnVisible)
        {
            passwordConfirmationInput.contentType = InputField.ContentType.Standard;
            passworCondirmationnRepead.contentType = InputField.ContentType.Standard;
        }
        else
        {
            passwordConfirmationInput.contentType = InputField.ContentType.Password;
            passworCondirmationnRepead.contentType = InputField.ContentType.Password;
        }

        passwordConfirmationInput.ForceLabelUpdate();
        passworCondirmationnRepead.ForceLabelUpdate();
    }
    



    public void Login()
    {

        if (usernameLogin.text != "" && passwordLogin.text != "")
        {
            Debug.Log(usernameLogin.text + " / " + passwordLogin.text);
            ApiConnection.Connection<User, LoginSetData>("login.php", new User("", usernameLogin.text, passwordLogin.text), (value) =>
            {

                if (value.success)
                {
                    if (activationControlToggle.isOn) // hesabý kaydet açýkmý
                    {

                        SaveToken token = new SaveToken { token = value.token };
                        FileSystem.JsonSave("Token", value.token);

                    }

                    GameManager.SetToken(value.token);

                    SceneManager.LoadScene("MainMenu");
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

            ApiConnection.Connection<SendToActivationCodeMail, LoginSetData>("login.php", new SendToActivationCodeMail(thisMail, ActivationCodeTxt.text.Trim()), (value) =>
            {

                if (value.success)
                {
                    GameManager.SetToken(value.token);
                    thisActivationCode = ActivationCodeTxt.text;
                    activationControlPanel.SetActive(false);
                    passwordConfirmationPanel.SetActive(true);
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

        thisMail = passwordResetMail.text;
        ApiConnection.Connection<SendMail, LoginSetData>("login.php", new SendMail { mail = thisMail }, (value) =>
        {

        });

        activationControlPanel.SetActive(true);

    }

    public void LogingeriPanelOpen()
    {
        signinPanel.SetActive(false );
       loginPanel.SetActive(false) ;
        menuPenel .SetActive(true) ;
    }
    public void ResetPasswordLoginOpen() 
    {
        passwordConfirmationPanel.SetActive(false);

        if (passworCondirmationnRepead.text == passwordConfirmationInput.text)
            ApiConnection.Connection<SendToActivationCodeMailPassword, LoginSetData>("login.php", new SendToActivationCodeMailPassword(thisMail, thisActivationCode, passworCondirmationnRepead.text), (value) =>
            {

                if (value.success)
                {

                    menuPenel.SetActive(true);
                    
                }
                else
                {

                    Debug.Log("ÞifreKaydedilemedi.");
                    passwordConfirmationPanel.SetActive(true);

                }

            });
        else
        {

            Debug.Log("Þifreler Eþleþmiyor.");
            passwordConfirmationPanel.SetActive(true);

        }
    }
    public void MailBackButton() 
    {
        activationControlPanel .SetActive(false);
        passwordConfirmationPanel .SetActive(true);
    }

    void Start()
    {
        menuPenel.SetActive(true);
    }

    [ContextMenu("deneme")]
    public void Deneme()
    {
        ApiConnection.Connection();
    }
    public void ExitMenuOpen()
    {
        quitMenu.SetActive(true);
    }
    public void QuitCancelMenuOpen()
    {
        quitMenu.SetActive(false);
        menuPenel.SetActive(true);
    }

    public void CloseTheGame()
    {
        Application.Quit();
    }

}
