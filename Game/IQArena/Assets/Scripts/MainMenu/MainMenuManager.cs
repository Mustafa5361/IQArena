using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;

public class MainMenuManager : MonoBehaviour
{

    [SerializeField] private GameObject mainMenu;
    [SerializeField] private GameObject profilemenu;
    [SerializeField] private GameObject quitMenu;
   
    


    public void MainMenuOpen()
    {
        profilemenu.SetActive(false);
        mainMenu.SetActive(true);
    }

    public void ProfileMenuOpen()
    { 
        mainMenu.SetActive(false);
        profilemenu.SetActive(true);   
    }

    public void ExitMenuOpen() 
    {
        mainMenu.SetActive(false );
        quitMenu.SetActive(true);
    }

    public void QuitCancelMenuOpen()
    {
        quitMenu.SetActive(false );
        mainMenu.SetActive(true );
    }

    public void LogOut()
    {
        SceneManager.LoadScene("Login");
    }
    public void CloseTheGame()
    {
        Application.Quit();
    }





    void Start()
    {
        
    }

    void Update()
    {
        
    }
}
