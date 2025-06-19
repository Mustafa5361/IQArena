using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class UnitBtnGetQuestion : MonoBehaviour
{

    int unitID;

    public void GetQuestions()
    {

        ApiConnection.Connection<SetUnit, GetQuestions>("adminPanel.php", new SetUnit(unitID), (value) =>
        {

            //sorular listenecek.

        });

    }

}

public class SetUnit
{

    public int unitID;

    public SetUnit(int unitID)
    {
        this.unitID = unitID;
    }

}

public class GetQuestions
{

    List<SingleQuesiton> questions = new List<SingleQuesiton>();

}
