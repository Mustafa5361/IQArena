using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;

public class AdminPanelManager : MonoBehaviour
{
    [SerializeField] private GameObject AdminPanel;
    [SerializeField] private GameObject Question;
    [SerializeField] private GameObject UsersMenu;
    [SerializeField] private GameObject QuitMenu;
    [SerializeField] private GameObject UnitMenu;

    public void unitMenu()
    {
        AdminPanel.SetActive(false);
        UnitMenu.SetActive(true);
    }

    public void unitMenuClose()
    {
        UnitMenu.SetActive(false);
        AdminPanel.SetActive(true);
    }
    public void QuestionOpen()
    {
        AdminPanel.SetActive(false);
        Question.SetActive(true);
    }

    public void QuestionClose()
    {
        Question.SetActive(false ); 
        AdminPanel.SetActive(true);
    }
    public void QuitMenuOpen()
    {
        AdminPanel.SetActive(false);
        QuitMenu.SetActive(true);
    }

    public void QuitCancelMenuOpen()
    {
        QuitMenu.SetActive(false);
        AdminPanel.SetActive(true);
    }
    public void CloseTheGame()
    {
        Application.Quit();
    }
    public void AdminLogOut()
    {
        SceneManager.LoadScene("Login");
    }

}
