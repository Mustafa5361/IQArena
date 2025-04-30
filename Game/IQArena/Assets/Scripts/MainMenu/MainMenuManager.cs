using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;
using UnityEngine.UI;

public class MainMenuManager : MonoBehaviour
{

    [SerializeField] private GameObject mainMenu;
    [SerializeField] private GameObject profilemenu;
    [SerializeField] private GameObject quitMenu;
    [SerializeField] private GameObject matchExpectionmenu;
    [SerializeField] private GameObject Ranknmenu;

    public Text timeText;

    public void matchExpectionmenuOpen()
    {

        StartCoroutine(roomExpection());
        matchExpectionmenu.SetActive(true);

    }
    IEnumerator roomExpection()
    {

        int elapsedTime = 0;

        while (true)
        {
            int minute = elapsedTime / 60;
            int second = elapsedTime % 60;

            timeText.text = string.Format("{0:00}:{1:00}",minute,second);

            if (elapsedTime % 3 == 0)
            {
               
                ApiConnection.Connection<WriteToken, ReadRoom>("roomControler.php", new WriteToken(GameManager.token), (value) =>
                {

                    if (value.success)
                    {

                        GameMenu.SetRoomID(value.roomID);
                        SceneManager.LoadScene("Game");

                    }

                });
                
            }

            yield return new WaitForSeconds(1f);

            elapsedTime++;

        }

    }

    public void matchExpectionmenuClose()
    {
        matchExpectionmenu.SetActive(false);
        mainMenu.SetActive(true);

    }
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
        GameManager.SetToken("");
        SceneManager.LoadScene("Login");
    }
    public void CloseTheGame()
    {
        Application.Quit();
    }

    public void RanKMenuOpen()
    {
        Ranknmenu.SetActive(true);
    }

    public void RanKMenuClose()
    {
        Ranknmenu.SetActive(false);    
    }

}
