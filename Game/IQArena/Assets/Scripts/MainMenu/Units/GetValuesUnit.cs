using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;
using UnityEngine.UI;

public class GetValuesUnit : MonoBehaviour
{

    [SerializeField] private Text unitName;
    private int unitID;

    public int UnitID
    {
        get { return unitID; }
    }

    public void SetData(int unitID, string Text)
    {

        this.unitID = unitID;
        unitName.text = Text;

    }

    public void UnitGetApi()
    {

        if (GetComponent<GetValuesUnit>() != null)
        {

            ApiConnection.Connection<GetUnitApi, GetSingleQuestion>("singlePlayer.php", new GetUnitApi(GetComponent<GetValuesUnit>().UnitID), (vales) =>
            {

                GameMenu.singleQuestions = new List<SingleQuesiton>();
                GameMenu.singleQuestions.AddRange(vales.questions);
                GameMenu.isSinglePlayer = true;

                SceneManager.LoadScene("Game");

            });

        }

    }

}

[System.Serializable]
public class GetValuesUnitList
{

    public List<GetValuesUnitApi> units = new List<GetValuesUnitApi>();

    [System.Serializable]
    public class GetValuesUnitApi
    {

        public string unitName;
        public int unitID;

    }

}
