Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\path\to\script\script.bat" & Chr(34), 0
Set WinScriptHost = Nothing