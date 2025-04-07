using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class LoginManager : MonoBehaviour
{

    [SerializeField] private GameObject menuPenel;
    [SerializeField] private GameObject loginPanel;
    [SerializeField] private GameObject signinPanel;
    [SerializeField] private GameObject passwordResetPanel;
    [SerializeField] private GameObject passwordConfirmationPanel;
    [SerializeField] private GameObject activationControlPanel;
    [SerializeField] private GameObject logingeriPanel;
    

    public void LoginPanelOpen()
    {
        passwordConfirmationPanel.SetActive(false);
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
