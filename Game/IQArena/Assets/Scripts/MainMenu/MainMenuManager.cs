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
    [SerializeField] private GameObject unitsPanel;
    [SerializeField] private GameObject unitsPanelListPanel;

    [SerializeField] private GameObject Ranknmenu; // listenin ana menüsü
    [SerializeField] private GameObject RankPanel; //gösterilen liste
    [SerializeField] private GameObject RankPlayerPlanel; // gösterilecek kiþiler

    [SerializeField] private GameObject MatchPanel; // geçmiþin göstrileceði liste
    [SerializeField] private GameObject MatchPlayerPlanel; // geçmiþ

    [SerializeField] private Text user;
    [SerializeField] private Text point;
    [SerializeField] private Text rank;

    [SerializeField] private GameObject unitBtn;
    private List<GameObject> unitBtns = new List<GameObject>();

    [SerializeField] private List<PlayerHistory> MatchHistory1;
    private List<GameObject> MatchHistoryObject = new List<GameObject>();

    private List<GameObject> PlayerRankObjekt = new List<GameObject>();

    public Text timeText;

    Coroutine expection;

    public void UnitsPanelOpen()
    {
        mainMenu.SetActive(false);

        ApiConnection.Connection<GetUnitApi, GetValuesUnitList>("singlePlayer.php", new GetUnitApi { unitID = 0 }, (value) =>
        {
            if (value.units == null || value.units.Count == 0)
            {
                Debug.LogWarning("Unit listesi boþ veya null.");
                return;
            }

            foreach (var item in value.units)
            {
                GameObject go = Instantiate(unitBtn, unitsPanelListPanel.transform);
                go.GetComponent<GetValuesUnit>().SetData(item.unitID, item.unitName);
                unitBtns.Add(go);
            }

        });

        unitsPanel.SetActive(true);
    }

    public void UnitsPanelClose()
    {
        unitsPanel.SetActive(false);
        
        foreach (var item in unitBtns)
        {
            Destroy(item.gameObject);
        }
        unitBtns.Clear();

        mainMenu.SetActive(true);
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

        ApiConnection.Connection<LoginGetData, ProfilData>("playerInformation.php", new LoginGetData { token = GameManager.Token }, (value) => 
        {

            user.text = value.userName;
            point.text = value.point;
            rank.text = value.cup;

            foreach (var matchHistory in value.matchHistories)
            {

                GameObject go = Instantiate(MatchPlayerPlanel, MatchPanel.transform);

                go.GetComponent<MatchHistoryData>().SetData(matchHistory.thisStatus, matchHistory.thisCup, matchHistory.thisUsername, matchHistory.enemyUsername,matchHistory.thisAnswerTime,matchHistory.enemyAnswerTime, matchHistory.thisPoint, matchHistory.enemyPoint);

                MatchHistoryObject.Add(go);

                height += (go.GetComponent<RectTransform>().sizeDelta.y + 31);

            }

        });

        

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

        ApiConnection.Connection<SetRank, PlayerRank>("playerInformation.php", new SetRank("rank"), (value) =>
        {

            foreach (var playerCup in value.ranks)
            {

                if (playerCup == null)
                    break;

                GameObject go = Instantiate(RankPlayerPlanel, RankPanel.transform);

                go.GetComponent<ArrangementData>().SetData(playerCup.username, playerCup.cup);

                PlayerRankObjekt.Add(go);

                height += (go.GetComponent<RectTransform>().sizeDelta.y + 31);

                Debug.Log(height);

            }

        });
        
        Ranknmenu.SetActive(true);
        RankPanel.GetComponent<RectTransform>().sizeDelta = new Vector2(0, height);

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