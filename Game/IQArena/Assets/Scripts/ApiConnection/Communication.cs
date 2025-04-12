public class SendToActivalionCode
{

    public string token;
    public string activationCode;
    public bool resetPassword;

    public SendToActivalionCode(string token, string activationCode, bool resetPassword)
    {
        this.token = token;
        this.activationCode = activationCode;
        this.resetPassword = resetPassword;
    }

}