using System.Collections;
using System.Collections.Generic;
using Unity.VisualScripting;
using UnityEngine;
using UnityEngine.SceneManagement;
using UnityEngine.UI;

public class MainMenuManager : MonoBehaviour
{

    [SerializeField] private GameObject mainMenu;
    [SerializeField] private GameObject profilemenu;
    [SerializeField] private GameObject quitMenu;
    [SerializeField] private GameObject matchExpectionmenu;

    [SerializeField] private GameObject Ranknmenu; // listenin ana menüsü
    [SerializeField] private GameObject RankPanel; //gösterilen liste
    [SerializeField] private GameObject RankPlayerPlanel; // gösterilecek kiþiler

    [SerializeField] private GameObject MatchPanel; // geçmiþin göstrileceði liste
    [SerializeField] private GameObject MatchPlayerPlanel; // geçmiþ

    [SerializeField] private List<PlayerHistory> MatchHistory1;
    private List<GameObject> MatchHistoryObject = new List<GameObject>();

    private List<GameObject> PlayerRankObjekt = new List<GameObject>();

    public Text timeText;

    Coroutine expection;

    public void SinglePlayerOpen()
    {
        SceneManager.LoadScene("Game");
    }

    public void matchExpectionmenuOpen()
    {

        expection = StartCoroutine(roomExpection());
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
               
                ApiConnection.Connection<WriteToken, ReadRoom>("roomControler.php", new WriteToken(GameManager.Token), (value) =>
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
        StopCoroutine(expection);
        matchExpectionmenu.SetActive(false);
        mainMenu.SetActive(true);

    }
    

    public void ProfileMenuOpen()
    {

        float height = 0;

        foreach (PlayerHistory matcchHistory in this.MatchHistory1)
        {

            GameObject go = Instantiate(MatchPlayerPlanel, MatchPanel.transform);

            go.GetComponent<MatchHistory>().SetData(matcchHistory.win,matcchHistory.cup,matcchHistory.player1UserName, matcchHistory.player2UserName, matcchHistory.player1Time, matcchHistory.player2Time, matcchHistory.player1Point,
                 matcchHistory.player2Point);

            MatchHistoryObject.Add(go);

            height += (go.GetComponent<RectTransform>().sizeDelta.y + 31);

        }

        RectTransform rt = MatchPanel.GetComponent<RectTransform>();
        Vector2 sizeDelta = rt.sizeDelta;
        sizeDelta.y = height;
        rt.sizeDelta = sizeDelta;
        mainMenu.SetActive(false);
        profilemenu.SetActive(true);   
    }

    public void ProfileMenuClose()
    {

        foreach (GameObject go in this.MatchHistoryObject)
        {
            Destroy(go);
        }

        MatchHistoryObject.Clear();

        profilemenu.SetActive(false);
        mainMenu.SetActive(true);
    }

    public void ExitMenuOpen() 
    {
        mainMenu.SetActive(false);
        quitMenu.SetActive(true);
    }

    public void QuitCancelMenuOpen()
    {
        quitMenu.SetActive(false);
        mainMenu.SetActive(true);
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
        mainMenu.SetActive(false);
        float height = 0;

        ApiConnection.Connection<string, PlayerRank>("playerInformation.php", "rank", (value) =>
        {
            foreach (var playerCup in value.ranks)
            {

                GameObject go = Instantiate(RankPlayerPlanel, RankPanel.transform);

                go.GetComponent<ArrangementData>().SetData(playerCup.username, playerCup.cup);

                PlayerRankObjekt.Add(go);

                height += (go.GetComponent<RectTransform>().sizeDelta.y + 31);

            }
        });

        RectTransform rt = RankPanel.GetComponent<RectTransform>();
        Vector2 sizeDelta = rt.sizeDelta;
        sizeDelta.y = height;
        rt.sizeDelta = sizeDelta;
        Ranknmenu.SetActive(true);

    }

    public void RanKMenuClose()
    {

        foreach(GameObject go in this.PlayerRankObjekt)
        {
            Destroy(go);
        }

        PlayerRankObjekt.Clear();

        Ranknmenu.SetActive(false);    
        mainMenu.SetActive(true);
    }

}