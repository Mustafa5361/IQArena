public class SendToActivationCode
{

    public string token;
    public string activationCode;
    public bool resetPassword;

    public SendToActivationCode(string token, string activationCode, bool resetPassword)
    {
        this.token = token;
        this.activationCode = activationCode;
        this.resetPassword = resetPassword;
    }

}

public class SendToActivationCodeMail
{

    public string mail;
    public string activationCode;

    public SendToActivationCodeMail(string mail, string activationCode)
    {
        this.mail = mail;
        this.activationCode = activationCode;
    }

}

public class SendToActivationCodeMailPassword
{

    public string mail;
    public string activationCode;
    public string password;

    public SendToActivationCodeMailPassword(string mail, string activationCode, string password)
    {
        this.mail = mail;
        this.activationCode = activationCode;
        this.password = password;
    }

}
