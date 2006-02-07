THE ITALIAN VOICE FOR ATALKER (ATutor 1.5.1 pl2 - ATalker 0.1)
 
Once You've completed the previous installations of Atalker, Atalker­_theme, FestivalTTS and Mbrola described on the official ATalker README: 

- install the italian language package from /packages dir inside the ATalker-it-addon.tar.gz archive;  
 
- Download the Italian-voices-package for Festival Text-to-Speech System at: 

http://www.pd.istc.cnr.it/Software/It-Festival/Italian-FESTIVAL.zip.

The available Italian voices are in lp_diphone.zip and pc_diphone.zip sub-archives (female and male voices respectively); 

- unzip the "Italian" directory placed inside the above sub-archive lp_diphone.zip (or pc_diphone.zip) and copy in 
[Festival-dir]/lib/voices/ (Where [Festival-dir] is your typical "/usr/share/festival/" directory);
- unzip the directory "ifd" placed inside the sub-archive "lex_ifd.zip" (it will generate the tree: festival/lib/dicts/ifd etc.) and 
  copy it in your own Festival dirtree ([Festival-dir]/lib/dicts/); 
- unzip the directory "italian_scm" placed inside the sub-archive "italian_scm.zip" of Italian-Festival.zip 
(it will generate the tree: festival/lib/italian_scm etc.) and copy it in 
"/usr/lib/festival/" (or equivalent path on your system, not on [Festival-dir]/lib/dicts/); ; 

To avoid "SIOD ERROR: could not open file [...]/synthesis.scm", 
modify the file: [Festival-dir]/lib/voices/italian/lp_diphone/festvox/lp_diphone.scm 
at line 119 (substitute the term "libdir" with "datadir"). For male voice the lp_diphone directory changes in pc_diphone.

To avoid "SIOD ERROR: unbound variable" during the setting-up of "lp_diphone" on interactive mode of Festival, 
find the string "da noi" (line 120) on the file: [Festival-dir]/lib/voices/italian/lp_diphone/festvox/lp_diphone.scm 
and rewrite the comment to a single line.

NOTE: On the two steps above, the "lp_diphone" directory changes in the "pc_diphone" one for the male voice.



SETTING UP MBROLA ITALIAN VOICES

The available Italian voices are in lp_mbrola.zip and pc_mbrola.zip sub-archives (female and male voices respectively); 

Copy the directory "lp_mbrola" found in lp_mbrola_unix.zip (placed inside the archive "Italian-Festival.zip" cited above) 
in [Festival-dir]/lib/voices/Italian/ (Where [Festival-dir] is your typical "/usr/share/festival/" directory);

download the file (for the female voice) at: http://tcts.fpms.ac.be/synthesis/mbrola/dba/it4/it4-010926.zip 
(http://tcts.fpms.ac.be/synthesis/mbrola/dba/it3/it3-010304.zip for the male voice) and unzip in [Festival-dir]/lib/voices/Italian/lp_mbrola/ (or pc_mbrola) 

NOTE: It's necessary moving the /it3 (or /it4) deflated directory [Festival-dir]/lib/voices/italian/lp_mbrola/ (or Pc_mbrola/).

Change the term "libdir" with "datadir" in: 
[Festival-dir]/lib/voices/italian/lp_mbrola/festvox/lp_mbrola.scm (line 106) (or in pc_mbrola.scm)
and rewrite the comment containing the term "moduli" (line 107) inside the same file on a single line, if it's on two.

Create a symbolical link in /usr/sbin/mbrola to the /usr/local/bin/mbrola file (for Apache relative addressing).


PATCHING ATALKER


1) Please, BACKUP ATUTOR. 

2) unzip the files in the "ATutor" directory placed inside the ATalker-italian.zip archive on your own installed ATutor directory (create and deflate directory and files to the existing one "ATutor" dirtree);


3) Execute the queries placed in "sql" directory (placed in Atalker-italian.zip) (after having deleted the suitable records if necessary) on ATUTOR DB;










