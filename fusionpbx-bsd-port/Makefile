# Created by: Richard Neese <r.neese@gmail.com>
# $FreeBSD: $

PORTNAME=	fusionpbx
DISTVERSION=	3.6.2
PORTREVISION=	1
CATEGORIES=	www
MASTER_SITES=	http://repo.fusionpbx.com/tarballs/ \
		${MASTER_SITE_GOOGLE_CODE}

MAINTAINER=	r.neese@gmail.com
COMMENT=	PHP5 Graphical User Interface for FreeSwitch

LICENSE=	MPL

USE=		sqlite
USE=		PHP=dom,filter,ftp,json,pcre,pdo,pdo_sqlite,posix,session,simplexml,xml,xmlreader,xmlwriter,xmlrpc,xsl,zlib

DEFAULT_VERSIONS=php=5

VARBASE=	/var

WRKSRC=		${WRKDIR}/${PORTNAME}
NO_BUILD=	yes

FUSIONPBX_USER?=	www
FUSIONPBX_GROUP?=	${FUSIONPBX_USER}

USERS=  ${FUSIONPBX_USER}
GROUPS= ${FUSIONPBX_GROUP}

OPTIONS_DEFINE=			PDOPGSQL

OPTIONS_GROUP=                  APPLICATIONS PROVISIONING
OPTIONS_GROUP_APPLICATIONS=     ADMINER BACKUP CALLBLOCK CALLBROADCAST CALLCENTER CALLFLOWS CALLS CLICKTOCALL CONFCENTERS \
				CONTACTS CONTENT DESTINATIONS DIALPLAN DPINBOUND DPOUTBOUND EDIT EXEC EXTENSIONS FAX FIFO \
				FOLLOWME GATEWAYS HOTDESKING IVRMENU LOGVIEWER LOGIN MODULES MUSICONHOLD PARK RECORDINGS \
				REGISTRATIONS RINGGROUPS SCHEMAS SERVICES SETTINGS SIPPROFILES SIPSTATUS SQLQUERY SYSTEM TIMECONDITIONS \
				TRAFFICGRAPH VARS VOICEMAILS XMLCDR

OPTIONS_GROUP_PROVISIONING=	DEVICES PROVISION

OPTIONS_MULTI=			THEMES WEBSERVER
OPTIONS_MULTI_THEMES=		ACCESSIBLE CLASSIC DEFAULT ENHANCED MINIMIZED

OPTIONS_MULTI_WEBSERVER=	APACHE NGINX

APACHE_DESC=		Apache Web Server (Corp Style Web Server)
NGINX_DESC=		Nginx Web Server(Small/Fast/Low Resource)
ADMINER_DESC=		superadmin easy database access tool
BACKUP_DESC=		pbx backup module (sql/dialplan/cdr's)
CALLBLOCK_DESC=		Used to block did's/ddi's to
CALLBROADCAST_DESC=	create/blast recordings to extensions
CALLCENTER_DESC=	Configure CallCenter Queues/Tiers
CALLFLOWS_DESC=		direct calls between two destinations
CALLS_DESC=		Display and interact with ActiveCalls
CLICKTOCALL_DESC=	Click Dial Contacts/Extensions/Devices
CONFCENTERS_DESC=	multi room confrences and room contol
CONTACTS_DESC=		Enter/Edit/Remove Company Contacts
CONTENT_DESC=		Advanced-Content Manager
DESTINATIONS_DESC=	DID/DDi Inbound Configurion
DEVICES_DESC=		List/Configure Devices for provisioning
DIALPLAN_DESC=		Display/Edit System DialPlan
DPINBOUND_DESC=		eidt/delete inbound dialplan
DPOUTBOUND_DESC=	outbound trunk dialplan  add/edit/delete
EDIT_DESC=		multi tools for editing
EXEC_DESC=		pages for executing (php/shells) commands
EXTENSIONS_DESC=	system user extensions configuration
FAX_DESC=		Faxing Services inbound/outbound
FIFO_DESC=		first in first out call queues system
FOLLOWME_DESC=		Redirect calls to other devices/extensions
GATEWAYS_DESC=		Inbound/Outbound Gate way Configuration
HOTDESKING_DESC=	login and recieve calls on any office phone
IVRMENU_DESC=		Interactive Voice Recorded menu System
LOGVIEWER_DESC=		Monitor the Freeswitch CLI
LOGIN_DESC=		User for usr/admin gui login
MODULES_DESC=		List modules enable/disable modules
MUSICONHOLD_DESC=	List/add/delete moh for system
PARK_DESC=		Call Parking
PROVISION_DESC=		Tool used to provision devices/ata/phones
RECORDINGS_DESC=	Upuload/Downloade/add/delete custom recordings
REGISTRATIONS_DESC=	List Sip/Device/Gateways registraions
RINGGROUPS_DESC=	Ring multi devices multi ways
SCHEMAS_DESC=		used to upgrade the database schema
SERVICES_DESC=		allows interaction with running processes
SETTINGS_DESC=		Default system settings
SIPPROFILES_DESC=	System sip Profies add/delete/edit
SIPSTATUS_DESC=		Display the Sip registrations/status for system
SQLQUERY_DESC=		interactively submit SQL queries to databases
SYSTEM_DESC=		System statius page
TIMECONDITIONS_DESC=	Set Time/Dates for Call Routing
TRAFFICGRAPH_DESC=	php graph monitor network interfaces
VARS_DESC=		Systems Vartiables page
VOICEMAILS_DESC=	VoiceMail Extensions/playback/redirect
XMLCDR_DESC=		System CDR page and statistics page
XMPP_DESC=		Configure XMPP to interact with with jabber servers
ACCESSIBLE_DESC=	Accessible theme
CLASSIC_DESC=		Classic theme
DEFAULT_DESC=		Default theme
ENHANCED_DESC=		Enhanced theme
MINIMIZED_DESC=		Minimal theme
PDOPGSQL_DESC=		Install POSTGRESQL PDO Connector

OPTIONS_DEFAULT=	ENHANCED NGINX

NO_OPTIONS_SORT=        yes
OPTIONS_SUB=            yes

SUB_LIST+=	FUSIONPBX_USER=${FUSIONPBX_USER}
PLIST_SUB+=	FUSIONPBX_USER=${FUSIONPBX_USER}
PLIST_SUB+=	FUSIONPBX_GROUP=${FUSIONPBX_GROUP}

APACHIE_USE=	apache24+

NGINX_RUN_DEPENDS=	${LOCALBASE}/sbin/nginx:${PORTSDIR}/www/nginx

PDOPGSQL_USES=	pgsql:9.3
PDOPGSQL_USE=	PHP=pgsql,pdo_pgsql

.include <bsd.port.options.mk>

do-install:
	@${FIND} ${WRKSRC} -name '*.php' -exec ${CHMOD} 664 {} +

	@${MKDIR} ${STAGEDIR}${WWWDIR} ${STAGEDIR}${DATADIR} ${STAGEDIR}${DBDIR} ${STAGEDIR}${ETCDIR}

	(cd ${WRKSRC} && \
	${COPYTREE_SHARE} . ${STAGEDIR}${WWWDIR} && cd ${STAGEDIR}${WWWDIR} && \
		${FIND} . -depth \( -path '*app*' -o -path '*themes*' \
		-o -path '*resources/install/scripts*' -o -path '*resources/install/sounds*' \
		-o -path '*resources/install/sql*' -o -path '*resources/templates/conf*' \
	-o -path '*resources/templates/provision*' \) -delete )
	@${MKDIR} ${STAGEDIR}${WWWDIR}/app ${STAGEDIR}${WWWDIR}/themes

	@${MKDIR} ${STAGEDIR}${EXAMPLESDIR}/install
	(cd ${WRKSRC}/resources/install && \
	${COPYTREE_SHARE} "scripts sounds sql" ${STAGEDIR}${EXAMPLESDIR}/install)

	@${MKDIR} ${STAGEDIR}${EXAMPLESDIR}/templates
	(cd ${WRKSRC}/resources/templates && \
	${COPYTREE_SHARE} "conf provision" ${STAGEDIR}${EXAMPLESDIR}/templates)

.for i in adminer backup call_block call_broadcast call_flows click_to_call contacts content destinations \
	devices dialplan edit exec extensions fax follow_me gateways hot_desking ivr_menu login log_viewer \
	modules music_on_hold park provision recordings registrations ring_groups schemas settings sip_profiles \
	sip_status time_conditions xml_cdr xmpp
.if ${PORT_OPTIONS:M${i:S/_//g:tu}}
	@cd ${WRKSRC}/app; ${FIND} ${i} \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/app
.endif
.endfor
.if ${PORT_OPTIONS:MCALLS}
	@cd ${WRKSRC}/app; ${FIND} calls calls_active \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/app
.endif

.if ${PORT_OPTIONS:MCALLCENTER}
	@cd ${WRKSRC}/app; ${FIND} call_center call_center_active \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/app
.endif

.if ${PORT_OPTIONS:MCONFCENTERS}
	@cd ${WRKSRC}/app; ${FIND} conference_centers conferences_active meetings \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/app
.endif

.if ${PORT_OPTIONS:MDPINBOUND}
	@cd ${WRKSRC}/app; ${FIND} dialplan_inbound \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/app
.endif

.if ${PORT_OPTIONS:MDPOUTBOUND}
	@cd ${WRKSRC}/app; ${FIND} dialplan_outbound \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/app
.endif

.if ${PORT_OPTIONS:MFIFO}
	@cd ${WRKSRC}/app; ${FIND} fifo fifo_list \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/app
.endif

.if ${PORT_OPTIONS:MVOICEMAILS}
	@cd ${WRKSRC}/app; ${FIND} voicemails voicemail_greetings \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/app
.endif

.for i in accessible classic default enhanced minimized
.if ${PORT_OPTIONS:M${i:S/_//g:tu}}
	@cd ${WRKSRC}/themes; ${FIND} ${i} \
		| ${CPIO} -pdm -R ${WWWOWN}:${WWWGRP} ${STAGEDIR}${WWWDIR}/themes
.endif
.endfor

	@${CHOWN} -R www:www ${STAGEDIR}${WWWDIR}
	@${CHOWN} www:www ${STAGEDIR}${DBDIR}

.include <bsd.port.mk>
