using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

public class ArrangementData : MonoBehaviour
{

    [SerializeField] private Text username;
    [SerializeField] private Text cup;

    public void SetData(string username, int cup)
    {

        this.username.text = username;
        this.cup.text = cup.ToString();

    }

}
