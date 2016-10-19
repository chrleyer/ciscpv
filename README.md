# ciscpv
####Display PVoutput.org statistical solar PV data on Cisco 79xx IP phones idle screen

This project requires you to have already an account at [PVoutput.org](http://pvoutput.org) and your inverter or data collection device is uploading data or data get’s pulled from the inverter to PVoutput. There are many ways to upload data from your solar system, see here for details. For our Samil inverter we are using this Python3 script from [Maarten Visscher](https://github.com/mhvis/solar).

![alt tag](https://raw.githubusercontent.com/chrleyer/ciscpv/master/SCR_20161019_001957.jpg)

In this article I describe only how to download the statistic portlet data from PVoutput.org using a PHP script, generating a PNG idle background image and configure a Cisco 7941 / 7942(G) / 7945 / 7961 / 7962(G) / 7965 / 7970 / 7971 / 7972 / 7975(G) and possibly other phones of the 79xx series. It may also work on the 7940/7960 but this possibly requires some changes because this phones needs GIF2XML converted files. Some details [here](https://supportforums.cisco.com/document/97571/changing-background-image-cisco-ip-phone-79407960).

The phones we are using are 7941G and 7961G with SIP firmware. If you are on SIP, probably using Asterisk or another SIP PBX like the Fritzbox, you are maybe already familiar with the configuration using XML files on your TFTP server. Phones with SCCP firmware on Cisco Callmanager can also be configured, but this means admin access to the Callmanager, because the idle URL configuartion can only be done at the server side. Details on Cisco SCCP idle URL config you may find [here](http://www.cisco.com/c/en/us/support/docs/voice-unified-communications/unified-communications-manager-callmanager/42573-idle-url.html#topic4).

First of all lets assume the server parameters:
```
Web Server IP address: 192.168.80.20 (FQDN may be used as well)
Web Server html folder: /cisco/idle
```

Copy the two files idle.php and createpng.php into your html folder, for example ../cisco/idle

Commit the following changes if you would like to have a background image layered behind the actual status data.

```php
// read background image (optional, enable the following two commands if you want a background image)
// Background image should reside in the same folder than this php file, 295x140 in size, Index Color Mode
$bg = imagecreatefrompng('background.png');
imagecopymerge($bg, $im, 0, 0, 0, 0, 295, 140, 100);
// Image output
	header("Content-type: image/png");
	imagepng($bg);
	imagedestroy($bg);
	?>
```
	
Phone configuration:

The Cisco IP Phones are requesting XML files and the idle URL should be set like this:
```
http://192.168.80.20/cisco/idle/idle.php
```
The idle.php generates an XML output for the phone to retreive the idle background image.

Open your SEP00260B5ABCDEF.cnf.xml phone config template on your TFTP server and edit the following:
Note: The filename for the above config file contains your phones MAC address, so this is only an example.

```
<idleTimeout>360</idleTimeout>
<idleURL>http://192.168.100.20/cisco/idle/idle.php</idleURL>
```

idleTimeout sets the phones screensaver timeout in seconds.

After the set timeout the phone will retreive the image from your webserver and displays your solar statistics. The refresh timer to determine how often the image should reload, can be set in the idle.php at “Refresh: 900”.

Be reminded that every refresh calls the portlet at PVoutput.org.

—

Additional configuration options:

1. You can also integrate the XML call in your services menue, for example the open79xx xml database services are supporting Link Objects, just create an object type link and point it to “http://SERVERIP-OR-FQDN/cisco/idle/idle.php”.

2. Also a nice solution is the usually unused (?) button at the phone. Just add this code to your SEP00…cnf.xml

```
<informationURL>
http://192.168.80.20/cisco/idle/idle.php
</informationURL>
```
