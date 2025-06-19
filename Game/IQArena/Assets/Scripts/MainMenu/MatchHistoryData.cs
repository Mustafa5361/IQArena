using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

public class MatchHistoryData : MonoBehaviour
{
    [SerializeField] private Text win;
    [SerializeField] private Text cup;
    [SerializeField] private Text player1UserName;
    [SerializeField] private Text player2UserName;
    [SerializeField] private Text Player1Time;
    [SerializeField] private Text Player2Time;
    [SerializeField] private Text Player1Point;
    [SerializeField] private Text Player2Point;


    public void SetData(string win, int cup, string player1UserName, string Player2UserName, string Player1Time, string Player2Time, int Player1Point, int Player2Point)
    {

        this.win.text = win;
        this.cup.text = cup.ToString();
        this.player1UserName.text = player1UserName.ToString();
        this.Player1Time.text = Player1Time;
        this.Player1Point.text = Player1Point.ToString();   
        this.player2UserName.text = Player2UserName.ToString(); 
        this.Player2Time.text = Player2Time;
        this.Player2Point.text = Player2Point.ToString();

    }
}
