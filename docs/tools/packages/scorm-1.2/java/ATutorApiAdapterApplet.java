/*
 * SCORM-1.2 API-Adapter Java Applet for ATutor
 * Copyright (c) Matthai Kurian
 *
 * Made for ATutor, the same license terms as for ATutor itself apply.
 *
 * This Applet handles communication between ATutor and SCORM-1.2
 * Sharable Content Objects (SCOs). Most communication is via Liveconnect.
 * CMI (Computer Managed Instruction) data is sent to ATutor through http POST
 * to an ATutor server side PHP script. SCORM-1.2 runtime behavior and CMI
 * datamodel management is done by the PfPLMS SCORM-1.2 API-Adapter Core. 
 */

import java.util.Hashtable;
import java.util.Enumeration;
import java.net.*;
import java.io.*;

public	class ATutorApiAdapterApplet
	extends java.applet.Applet
	implements ch.ethz.pfplms.scorm.api.ApiAdapterInterface
{
	private	ch.ethz.pfplms.scorm.api.ApiAdapter core;

	private Hashtable ATutorScoCmi  = new Hashtable();

	private String  ATutorStudentId;
	private String  ATutorStudentName;

	private String  ATutorScoId;
	private String  ATutorPreparedScoId;

	private boolean isVerbose   = false;

	public ATutorApiAdapterApplet () {
		core = new ch.ethz.pfplms.scorm.api.ApiAdapter ();
	}

	public	final void init () {
		if (getParameter("verbose") != null) isVerbose = true;
		ATutorStudentId     = getParameter ("student_id");
		ATutorStudentName   = getParameter ("student_name");
		say ("cmi.core.student_id=" +ATutorStudentId);
		say ("cmi.core.student_name=" +ATutorStudentName);
	}

	private final void say (String s) {
		if (isVerbose) System.out.println (s);
	}

	private final static String decode (String es) {

		String s = es.replace('+', ' ');
		int l = s.length();

		int a = 0; 

		StringBuffer rv = new StringBuffer(l);
		byte[] b = null;

		try {
			int i;
			while ((i = s.indexOf('%', a)) >= 0) {
				rv.append(s.substring(a, i));
				a = i;

				while (i+2 < l && s.charAt(i) == '%') i+=3;

				if (b == null || b.length < (i-a)/3) {
					b = new byte[((i-a)/3)];
				}

				int x = 0;
				for (; a < i; a+=3) {
					b[x++] = (byte) Integer.parseInt(
						s.substring(a+1, a+3), 16
					);
				}
				rv.append(new String(b, 0, x, "utf-8"));
			}

			if (a < l) rv.append(s.substring(a));
			return rv.toString();

		} catch (Exception e) {
			return "";
		}
	}

	/*
	 * Methods for ATutor to call via Liveconnect
	 */

	public	final void ATutorPrepare (String sco_id) {
		URLConnection po;
		ATutorScoCmi.clear();
		StringBuffer P = new StringBuffer();
		P.append ("&sco_id="+sco_id);
		say ("Retreiving cmi for sco="+sco_id+" from ATutor server");
		try {
			//po = (HttpURLConnection) ( new java.net.URL (
			po = (URLConnection) ( new java.net.URL (
				getCodeBase().toString() + "read.php"
			)).openConnection();

			po.setRequestProperty (
				"Content-Type",
				"application/x-www-form-urlencoded"
			);
			po.setRequestProperty (
				"Content-Length",
				Integer.toString (P.length())
			);
			po.setDoOutput (true);
			po.setUseCaches (false);
			//po.setRequestMethod ("POST");
			po.setAllowUserInteraction (false);

			OutputStream os = po.getOutputStream();
			os.write (P.toString().getBytes());
			os.flush ();
			os.close ();

			BufferedReader br = new BufferedReader(
					new InputStreamReader(
						po.getInputStream ()
					)
			);
			String s, l, r;
			while ((s=br.readLine())!=null) {
				if (s.indexOf('=') == -1) continue;
				l = s.substring (0, s.indexOf('='));
				r = s.substring (s.indexOf('=')+1, s.length());
				r = decode (r);
				say (" "+l + "="+r);
				ATutorScoCmi.put (l,r);
			}
		} catch (Exception e) {
			say ("ATutor cmi retrieval failed.");
			say (e.toString());
		}
		ATutorPreparedScoId = sco_id;
		say ("Done. Note: this was cmi for the next sco ("+sco_id+") to launch.");
	}

	public	final void ATutorReset (String s) {
		if (s != null && s.equals(ATutorScoId)) {
			ATutorScoId = null;
			core.reset();
			say ("Reset by ATutor client.");
		} 
	}

	private final String ATutorCommit (boolean fin) {

		if (ATutorScoId == null) return "false"; 

		core.transEnd();
		StringBuffer P = new StringBuffer();
		Hashtable ins = core.getTransNew ();
		Hashtable mod = core.getTransMod ();
		if (fin) {
			Object o = core.sysGet ("cmi.core.entry");
			if (o != null && o.toString().equals ("ab-initio")) {
				mod.put ("cmi.core.entry", "");
			}
		} else {
			core.transBegin();
		}

		P.append ("&sco_id=" +ATutorScoId);

		int i=0;
		for (Enumeration e = ins.keys(); e.hasMoreElements(); i++) {
			Object l = e.nextElement();
			Object r = ins.get(l);
			P.append("&iL["+i+"]="+l.toString());
			P.append("&iR["+i+"]="+URLEncoder.encode(r.toString()));
		}

		int u=0;
		for (Enumeration e = mod.keys(); e.hasMoreElements(); u++) {
			Object l = e.nextElement();
			Object r = mod.get(l);
			P.append("&uL["+u+"]="+l.toString());
			P.append("&uR["+u+"]="+URLEncoder.encode(r.toString()));
		}

		if (i == 0 && u == 0) {
			say ("Nothing to commit.");
			return "true";
		}

		//HttpURLConnection po;
		URLConnection po;

		try {
			//po = (HttpURLConnection) ( new java.net.URL (
			po = (URLConnection) ( new java.net.URL (
				getCodeBase().toString()
				+ "write.php"
			)).openConnection();

			po.setRequestProperty (
				"Content-Type",
				"application/x-www-form-urlencoded"
			);
			po.setRequestProperty (
				"Content-Length",
				Integer.toString (P.length())
			);
			po.setDoOutput (true);
			po.setUseCaches (false);
			//po.setRequestMethod ("POST");
			po.setAllowUserInteraction (false);

			OutputStream os = po.getOutputStream();
			os.write (P.toString().getBytes());
			os.flush ();
			os.close ();

			BufferedReader r = new BufferedReader(
					new InputStreamReader(
						po.getInputStream ()
					)
			);
			try {
				String s;
				while ((s=r.readLine())!=null) {
					say(s);
				}
			} catch (EOFException ok) {}
			return "true";

		} catch (Exception e) {
			say ("ATutor cmi storage failed.");
			say (e.toString());
			return "false";
		}
	}

	public	final String ATutorGetValue (String l) {
		String rv = core.LMSGetValue (l);
		say ("ATutorGetValue("+l+")="+rv);
		return rv;
	}

	/*
	 * Liveconnect interface methods for SCO
	 */

	public	final String LMSInitialize (String s) { 
		String rv = core.LMSInitialize(s);
		say ("LMSInitialize("+s+")="+rv);
		if (rv.equals("false")) return rv;
		core.reset();
		rv = core.LMSInitialize(s);
		ATutorScoId = ATutorPreparedScoId;
		core.sysPut ("cmi.core.student_id",   ATutorStudentId);
		core.sysPut ("cmi.core.student_name", ATutorStudentName);
		core.sysPut (ATutorScoCmi);
		core.transBegin();
		return rv;
	}

	public	final String LMSCommit (String s) {
		String rv = core.LMSCommit(s);
		if (rv.equals("false")) return rv;
		rv = ATutorCommit(false); 
		say ("LMSCommit("+s+")="+rv);
		return rv;
	}

	public	final String LMSFinish (String s) {
		String rv = core.LMSFinish(s);
		say ("LMSFinish("+s+")="+rv);
		if (rv.equals("false")) return rv;
		rv = ATutorCommit(true);
		ATutorScoId = null;
		core.reset();
		return rv;
	}

	public	final String LMSGetDiagnostic (String e) {
		String rv = core.LMSGetDiagnostic (e);
		say ("LMSGetDiagnostic("+e+")="+rv);
		return rv;
	}

	public	final String LMSGetErrorString (String e) {
		String rv = core.LMSGetErrorString (e);
		say ("LMSGetErrorString("+e+")="+rv);
		return rv;
	}

	public	final String LMSGetLastError () {
		String rv = core.LMSGetLastError ();
		say ("LMSLastError()="+rv);
		return rv;
	}

	public	final String LMSGetValue (String l) {
		String rv = core.LMSGetValue (l);
		say ("LMSGetValue("+l+")="+rv);
		return rv;
	}

	public	final String LMSSetValue (String l, String r) {
		String rv = core.LMSSetValue (l, r);
		say ("LMSSetValue("+l+"="+r+")="+rv);
		return rv;
	}
}
