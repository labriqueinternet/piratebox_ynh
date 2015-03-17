# PirateBox
## Overview

PirateBox app for [YunoHost](http://yunohost.org/).

Based on [DropCenter](http://projet.idleman.fr/dropcenter/) (the project was patched in order to remove the authentication mechanism and some other useless features).

## Features

* Open wifi Access Point (AP)
* Once connected to the AP, you can go on *http://pirate.box*, or *http://www.madhouse.gov* or even *http://lqksjdhfkljhsdf.qsdf*
* All destinations lead to the PirateBox web page
* No authentication required for uploading, downloading or deleting (lawless zone)
* Of course, works without internet connection

## Requirements

You have to install the [Wifi Hotspot app for YunoHost](https://github.com/jvaubourg/hotspot_ynh) before and disable the wifi secure access mode thanks to the friendly web interface.

## How It Works ##

Explanations:

1. all packets to port 53 are redirected to the port 4253,
2. a fake DNS resolver listens on the port 4253, and systematically responds the IPv4 address of the server (a fake DNS resolver is mandatory for responding to any requests, without internet connection),
3. a MASQUERADE rule allows the fake DNS to respond in place of the initially requested resolver,
4. all packets to port 80 are redirected to the port 4280,
5. a Nginx vhost listens on the port 4280, and redirect to the PirateBox web page (when the requested domain corresponds to the one used by the PirateBox, a reverse-proxy to the port 80 is used).

## Limitations ##

* If the user requests web sites he used to consult once connected, his browser may have a DNS cache entry for it (60s with Firefox) - but there is no problem in the other way because the fake DNS always responds with a TTL of 1s
* IPv4-only because the NAT table is not available for IPv6 before the kernel 3.8 (not in Debian stable for now)
* Don't redirect to the PirateBox web page with HTTPS requests (in order to avoid wrong certificates and to allow to use the YunoHost administration)
* The PirateBox is not HTTPS compliant, but it's not a problem because there is no privacy issues with a such free app
