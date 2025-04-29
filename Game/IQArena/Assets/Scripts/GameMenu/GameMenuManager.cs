using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;

public class GameMenu : MonoBehaviour
{
    
    [SerializeField] private GameObject Menu;

    public void MainMenuOpen()
    {
        SceneManager.LoadScene("MainMenu");
    }
   
    void Start()
    {
        
    }

    // Update is called once per frame
    void Update()
    {
        
    }
}
