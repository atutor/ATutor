
function popup(url, name, attributes) {
    if (url.indexOf('?') != -1) {
        url += '&_popup=true';
    } else {
        url += '?_popup=true';
    }
    p=window.open(url, name , attributes);
    if (p.opener == null) {
        p.opener = window;
    }
    if (window.focus) {
        p.focus();
    }
}

function popup_resizeTo( idOfDiv ) {
	var oH = getRefToDivMod( idOfDiv ); if( !oH ) { return false; }
	var x = window; x.resizeTo( screen.availWidth, screen.availWidth );
	var oW = oH.clip ? oH.clip.width : oH.offsetWidth;
	var oH = oH.clip ? oH.clip.height : oH.offsetHeight; if( !oH ) { return false; }
	x.resizeTo( oW + 200, oH + 200 );
	var myW = 0, myH = 0, d = x.document.documentElement, b = x.document.body;
	if( x.innerWidth ) { myW = x.innerWidth; myH = x.innerHeight; }
	else if( d && d.clientWidth ) { myW = d.clientWidth; myH = d.clientHeight; }
	else if( b && b.clientWidth ) { myW = b.clientWidth; myH = b.clientHeight; }
	if( window.opera && !document.childNodes ) { myW += 16; }
	//second sample, as the table may have resized
	var oH2 = getRefToDivMod( idOfDiv );
	var oW2 = oH2.clip ? oH2.clip.width : oH2.offsetWidth;
	var oH2 = oH2.clip ? oH2.clip.height : oH2.offsetHeight;
	x.resizeTo( oW2 + ( ( oW + 200 ) - myW ), oH2 + ( (oH + 200 ) - myH ) );
}



function popup_close(refreshOpener) {
    if (window.opener) {
       if (refreshOpener) {
          setTimeout("window.opener.location.reload()", 900);
       }
       setTimeout("window.close()",1100);
    }
}


function getRefToDivMod( divID, oDoc ) {
	if( !oDoc ) { oDoc = document; }
	if( document.layers ) {
		if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
			for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
				y = getRefToDivNest(divID,oDoc.layers[x].document); }
			return y; } }
	if( document.getElementById ) { return oDoc.getElementById(divID); }
	if( document.all ) { return oDoc.all[divID]; }
	return oDoc[divID];
}
function testURL(form, inputURL)
	{
		var test=document.forms[form].elements[inputURL].value;
		
		errorMsg = "The '{0}' field is empty";
		errorMsg = errorMsg.split("'")
		errorMsg = errorMsg[0] + inputURL + errorMsg[2];

		if (test=="") alert (errorMsg);
		else
		{
			if (test.indexOf("://") < 0)
			{
			   test= "http://" + test;
               document.forms[form].elements[inputURL].value = test;
			}
			newwin = window.open(test, 'newwin', '');
		}
    }

function testImage(img, form, inputURL)
	{
		var test=document.forms[form].elements[inputURL].value;
		
		errorMsg = "The " + inputURL + " field is empty";

		if (test=="") alert (errorMsg);
		else
		{
			if (test.indexOf("://") < 0)
			{
			   test= "http://" + test;
               document.forms[form].elements[inputURL].value = test;
			}
			img.src = test;
		}
    }

function confirmDelete(obj) {
    var msg = "Are you sure you want to delete this " + obj + "?";
    ans = confirm(msg);
    if (ans) {
        return true;
    } else {
        return false;
    }
}

function deleteItem(formName, itemName, item) {
    var deleteItemName = "_delete" + itemName;
    if (confirmDelete(itemName)) {
        MyForm = document.forms[formName];
        MyForm.elements[deleteItemName].value = item;
        MyForm.submit();
    }
}

function editItem(formName, itemName, item) {
    var editItemName = "_edit" + itemName;
    MyForm = document.forms[formName];
    MyForm.elements[editItemName].value = item;
    MyForm.submit();
}

function printDate(d){
	document.writeln(d.substring(7,17));
}

/* This function is used to get cookies */
function getCookie(name) {
    var prefix = name + "="
    var start = document.cookie.indexOf(prefix)

    if (start==-1) {
        return null;
    }
   
    var end = document.cookie.indexOf(";", start+prefix.length)
    if (end==-1) {
        end=document.cookie.length;
    }

    var value=document.cookie.substring(start+prefix.length, end)
    return unescape(value);
}

/* This function is used to delete cookies */
function deleteCookie(name,path,domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}

function checkLimit(limitField, limitNum) {
	if (limitField.value.length > limitNum){
		alert("You have exceeded the number of characters for this field");
		limitField.value = limitField.value.substring(0, limitNum-1);
	} 
		
}

function limitCopy(limitField, limitNum) {
	if (limitField.value.length > limitNum)	limitField.value = limitField.value.substring(0, limitNum);
}

function confirmPost() {
    if (confirm("Posting this composite review makes it appear on the material detail page and available to the public.  Are you sure you want to continue?")) {
        return true;
    } else {
        return false;
    }
}

function confirmUnpost() {
    if (confirm("Unposting this review will put this material back into the workflow process.  Are you sure you want to continue?")) {
        return true;
    } else {
        return false;
    }
}

    function showOther(mySelect, targetId) {
        var selected = mySelect.options[mySelect.selectedIndex].text;
        if (selected == 'Other') {
            if (document.getElementById) {
                target = document.getElementById(targetId);
                if (target.style.display == "none"){
                    target.style.display = "";
                }
            }
        } else {
            if (document.getElementById) {
                target = document.getElementById(targetId);
                if (target.style.display != "none"){
                    target.style.display = "none";
                }
            }
        }
    }

function cat(id, parentId, name) {
   this.id = id;
   this.parentId = parentId;
   this.name = name;
}

var cats = new Array(
new cat('','-1','All'),


new cat('2175','','Arts'),

new cat('2176','2175','Art History'),

new cat('2177','2175','Cinema'),

new cat('2178','2175','Dance'),

new cat('2179','2175','Fine Arts'),

new cat('2180','2175','General'),

new cat('2181','2175','Music'),

new cat('2182','2181','Aural Skill and Ear Training'),

new cat('2183','2181','Composition'),

new cat('2184','2181','Music Appreciation'),

new cat('2185','2181','Music Education'),

new cat('2186','2181','Music History'),

new cat('2187','2181','Music Technology'),

new cat('2188','2181','Performance'),

new cat('2189','2181','Theory and Analysis'),

new cat('2190','2181','World Music'),

new cat('2191','2175','Photography'),

new cat('2192','2175','Theatre'),

new cat('2193','2192','Critical Strategies'),

new cat('2194','2192','Design and Technology'),

new cat('2195','2192','Management'),

new cat('2196','2192','Music Theatre and Dance'),

new cat('2197','2192','Performing'),

new cat('2198','2192','Playwriting'),

new cat('2199','2192','Theatre Artists and Companies'),

new cat('2200','2192','Theatre History'),

new cat('2201','2192','Theatre for Specific Audiences'),

new cat('2202','','Business'),

new cat('2203','2202','Accounting'),

new cat('2204','2203','Accounting Education'),

new cat('2213','2203','Tax'),

new cat('2205','2203','Accounting Information Systems'),

new cat('2206','2203','Auditing'),

new cat('2207','2203','Ethics'),

new cat('2208','2203','Financial'),

new cat('2209','2203','International'),

new cat('2210','2203','Introductory'),

new cat('2211','2203','Managerial'),

new cat('2212','2203','Not for Profit'),

new cat('2259','2202','Marketing'),

new cat('2260','2259','Advertising'),

new cat('2261','2259','Business Marketing'),

new cat('2262','2259','Consumer Behavior'),

new cat('2263','2259','General'),

new cat('2264','2259','International'),

new cat('2265','2259','Market Research'),

new cat('2266','2259','Sales'),

new cat('2214','2202','Business Law'),

new cat('2215','2202','E-Commerce'),

new cat('2216','2202','Economics'),

new cat('2217','2216','Agricultural and Natural Resources'),

new cat('2226','2216','Industrial Organization'),

new cat('2227','2216','International'),

new cat('2228','2216','Labor and Demographics'),

new cat('2229','2216','Law and Economics'),

new cat('2230','2216','Macro'),

new cat('2231','2216','Mathematical and Quant.'),

new cat('2232','2216','Micro'),

new cat('2233','2216','Public'),

new cat('2234','2216','Urban, Rural and Regional'),

new cat('2218','2216','Bus Adm and Bus Mktg'),

new cat('2219','2216','Econometrics'),

new cat('2220','2216','Economic Development'),

new cat('2221','2216','Economic Systems'),

new cat('2222','2216','Financial'),

new cat('2223','2216','General'),

new cat('2224','2216','Health, Ed and Welfare'),

new cat('2225','2216','History of Ec. Thought'),

new cat('2235','2202','Finance'),

new cat('2236','2235','Corporate'),

new cat('2245','2235','Real Estate'),

new cat('2237','2235','Derivatives'),

new cat('2238','2235','Financial Institutions'),

new cat('2239','2235','Financial Markets'),

new cat('2240','2235','Insurance'),

new cat('2241','2235','International'),

new cat('2242','2235','Investments'),

new cat('2243','2235','Money and Banking'),

new cat('2244','2235','Personal Finance'),

new cat('2246','2202','General'),

new cat('2247','2202','International Business'),

new cat('227461','2247','Country Information and Geography'),

new cat('227462','2247','Cross Cultural Management/OB'),

new cat('227463','2247','Export and International Trade'),

new cat('227464','2247','Globalization'),

new cat('227465','2247','Human Resources in a Global Environment'),

new cat('227466','2247','International Case Studies'),

new cat('227467','2247','International Finance'),

new cat('227468','2247','International Marketing'),

new cat('227469','2247','International Project Management'),

new cat('227470','2247','Multicultural Issues Diversity'),

new cat('2248','2202','Management'),

new cat('2250','2248','Conflict Resolution'),

new cat('2251','2248','Entrepreneurship'),

new cat('2252','2248','Ethics'),

new cat('2253','2248','Human Resources'),

new cat('2254','2248','International'),

new cat('2255','2248','Org Behavior and Development'),

new cat('2256','2248','Production and Oper Mgnt'),

new cat('2257','2248','Project Management'),

new cat('2258','2248','Strategy'),

new cat('2249','2202','Management Information Systems'),

new cat('2267','','Education'),

new cat('2268','2267','General'),

new cat('2269','2267','Library and Information Services'),

new cat('2270','2269','General'),

new cat('2271','2269','Information Literacy'),

new cat('2272','2269','Information Retrieval'),

new cat('2273','2269','Information Technology'),

new cat('2274','2269','Issues in Librarianship'),

new cat('2275','2269','Library Specialties'),

new cat('2276','2267','TeacherEd'),

new cat('2277','2276','Classroom Management'),

new cat('2278','2276','Diversity and Multicultural Ed'),

new cat('2279','2276','Educational Foundations'),

new cat('2280','2276','Educational Psychology'),

new cat('2281','2276','Educational Research'),

new cat('2282','2276','Instructional Technology'),

new cat('2283','2276','Special Education'),

new cat('2284','2276','Student Assessment'),

new cat('2285','2276','Teaching Methods'),

new cat('2286','2285','Arts'),

new cat('2287','2285','English'),

new cat('2288','2285','Foreign Language'),

new cat('2289','2285','Mathematics'),

new cat('2290','2285','Physical Education'),

new cat('2291','2285','Reading and Language Arts'),

new cat('2292','2285','Science'),

new cat('2293','2285','Social Science'),

new cat('2294','2285','Vocational Education'),

new cat('2295','2267','Teaching and Technology'),

new cat('2296','2295','Accessibility'),

new cat('2298','2295','Assessment and Evaluation'),

new cat('2299','2295','Best Teaching Practices'),

new cat('2300','2299','Case Studies'),

new cat('2301','2299','Cooperative Learning'),

new cat('2302','2299','Lecture and Presentation'),

new cat('2303','2299','Multicultural and Diversity'),

new cat('2304','2299','Online Communications'),

new cat('2305','2299','Other'),

new cat('2306','2299','Problem Based Learning'),

new cat('2307','2299','Service Learning'),

new cat('2308','2295','Instructional Design'),

new cat('2309','2295','Policies'),

new cat('2310','2309','Intellectual Property'),

new cat('2311','2309','Other'),

new cat('2312','2295','Scholarship of Teaching and Learning'),

new cat('2313','2295','Selecting and Using Tools'),

new cat('2314','2313','Communication Tools'),

new cat('2315','2313','Course Management Tools'),

new cat('2316','2315','Angel'),

new cat('2317','2315','Blackboard'),

new cat('2318','2315','Desire2Learn'),

new cat('2319','2315','Other'),

new cat('2320','2315','WebCT'),

new cat('2321','2313','Hardware and Networks'),

new cat('2322','2313','Multimedia Tools'),

new cat('2323','2313','Other'),

new cat('2324','2313','Using MERLOT'),

new cat('2325','2313','Web Page Editors'),

new cat('2326','2295','Support and Training'),

new cat('2327','','Humanities'),

new cat('2328','2327','General'),

new cat('2329','2327','History'),

new cat('2330','2329','Area Studies'),

new cat('2331','2330','Africa'),

new cat('2332','2331','Central Africa'),

new cat('2333','2331','East Africa'),

new cat('2334','2331','North Africa'),

new cat('2335','2331','Southern Africa'),

new cat('2336','2331','West Africa'),

new cat('2337','2330','Americas'),

new cat('2338','2337','Brazil'),

new cat('2339','2337','Canada'),

new cat('2340','2337','Caribbean'),

new cat('2341','2337','Central America'),

new cat('2342','2337','Mexico'),

new cat('2343','2337','South America'),

new cat('2344','2337','United States'),

new cat('2345','2344','1789-1865'),

new cat('2346','2344','1865-1900'),

new cat('2347','2344','20th Century'),

new cat('2348','2344','American Revolution'),

new cat('2349','2344','Civil War'),

new cat('2350','2344','Colonial'),

new cat('2351','2344','Pre-Columbian'),

new cat('2352','2330','Asia'),

new cat('2353','2352','Central Asia'),

new cat('2354','2352','China'),

new cat('2355','2352','Eastern'),

new cat('2356','2352','Japan'),

new cat('2357','2352','Korea'),

new cat('2358','2352','Middle East'),

new cat('2359','2352','South Asia'),

new cat('2360','2352','Southeast Asia'),

new cat('2361','2330','Europe'),

new cat('2362','2361','Balkans'),

new cat('2371','2361','Italy'),

new cat('2372','2361','Northern'),

new cat('2373','2361','Scandinavia'),

new cat('2374','2361','Southern'),

new cat('2375','2361','Western'),

new cat('2363','2361','Benelux'),

new cat('2364','2361','Eastern'),

new cat('2365','2361','European Union'),

new cat('2366','2361','France'),

new cat('2367','2361','Germany and Central Europe'),

new cat('2368','2361','Great Britain and Ireland'),

new cat('2369','2361','Greece'),

new cat('2370','2361','Iberia'),

new cat('2376','2330','Oceania'),

new cat('2377','2376','Australia'),

new cat('2378','2376','New Zealand'),

new cat('2379','2376','Pacific Islands'),

new cat('2380','2330','World Systems'),

new cat('2381','2329','Resources'),

new cat('2382','2381','Document Archives'),

new cat('2383','2381','Image and Photographic Archives'),

new cat('2384','2381','Map Archives'),

new cat('2385','2329','Theory and Method'),

new cat('2386','2385','Epistemology of History'),

new cat('2387','2385','Historiography'),

new cat('2388','2385','History Education'),

new cat('2389','2385','Methods'),

new cat('2390','2385','Philosophy of History'),

new cat('2391','2329','Topical'),

new cat('2392','2391','African-American'),

new cat('2401','2391','Genealogy'),

new cat('2402','2391','Hispanic and Latino American'),

new cat('2403','2391','Intellectual'),

new cat('2404','2403','Diplomatic'),

new cat('2405','2403','Economic'),

new cat('2406','2391','Labour'),

new cat('2407','2391','Legal and Constitutional'),

new cat('2408','2391','Local and Regional'),

new cat('2409','2391','Military'),

new cat('2410','2391','Native American'),

new cat('2411','2391','Political'),

new cat('2412','2391','Popular Culture'),

new cat('2393','2391','Archaeology'),

new cat('2413','2391','Popular Culture'),

new cat('2414','2391','Public History'),

new cat('2415','2391','Race and Ethnicity'),

new cat('2416','2391','Religion'),

new cat('2417','2391','Rural'),

new cat('2418','2391','Science, Medicine, and Technology'),

new cat('2419','2391','Sexuality'),

new cat('2420','2391','Slavery'),

new cat('2421','2391','Social'),

new cat('2422','2391','Subaltern'),

new cat('2394','2391','Art History'),

new cat('2423','2391','Wars'),

new cat('2424','2391','Women'),

new cat('2425','2391','World and Global'),

new cat('2395','2391','Biography'),

new cat('2396','2391','Business and Economics'),

new cat('2397','2391','Class'),

new cat('2398','2391','Education'),

new cat('2399','2391','Environmental History'),

new cat('2400','2391','Gender'),

new cat('2426','2327','Language and Literature'),

new cat('2427','2426','Communications'),

new cat('2436','2426','Rhetoric'),

new cat('2437','2426','Speech'),

new cat('2428','2426','Composition'),

new cat('2429','2426','Criticism'),

new cat('2430','2426','Drama and Theatre'),

new cat('2431','2426','History'),

new cat('2432','2426','Journalism'),

new cat('2433','2426','Linguistics'),

new cat('2434','2426','Literature'),

new cat('2435','2426','Poetry'),

new cat('2438','2327','Philosophy'),

new cat('2439','2327','Religion'),

new cat('2440','2327','World Languages'),

new cat('2441','2440','Arabic'),

new cat('2442','2441','Culture'),

new cat('2443','2441','Language'),

new cat('2444','2441','Teacher Resources'),

new cat('2445','2441','culture'),

new cat('2486','2440','Korean'),

new cat('2487','2486','Culture'),

new cat('2488','2486','Language'),

new cat('2489','2486','Teacher Resources'),

new cat('2490','2486','culture'),

new cat('2491','2440','Latin'),

new cat('2492','2491','Culture'),

new cat('2493','2491','Language'),

new cat('2494','2491','Teacher Resources'),

new cat('2495','2491','culture'),

new cat('2496','2440','Less Commonly Taught Languages'),

new cat('2497','2440','Multilingual Resources'),

new cat('2498','2440','Portuguese'),

new cat('2499','2498','Culture'),

new cat('2500','2498','Language'),

new cat('2501','2498','Teacher Resources'),

new cat('2502','2498','culture'),

new cat('2503','2440','Russian'),

new cat('2504','2503','Culture'),

new cat('2505','2503','Language'),

new cat('2506','2503','Teacher Resources'),

new cat('2507','2503','culture'),

new cat('2508','2440','Spanish'),

new cat('2509','2508','Culture'),

new cat('2510','2508','Language'),

new cat('2511','2508','Teacher Resources'),

new cat('2512','2508','culture'),

new cat('2446','2440','Chinese'),

new cat('2447','2446','Culture'),

new cat('2448','2446','Language'),

new cat('2449','2446','Teacher Resources'),

new cat('2450','2446','culture'),

new cat('2451','2440','ESL'),

new cat('2452','2451','Culture'),

new cat('2453','2451','Language'),

new cat('2454','2451','Teacher Resources'),

new cat('2455','2451','culture'),

new cat('2456','2440','French'),

new cat('2457','2456','Culture'),

new cat('2458','2456','Language'),

new cat('2459','2456','Teacher Resources'),

new cat('2460','2456','culture'),

new cat('2461','2440','German'),

new cat('2462','2461','Culture'),

new cat('2463','2461','Language'),

new cat('2464','2461','Teacher Resources'),

new cat('2465','2461','culture'),

new cat('2466','2440','Greek'),

new cat('2467','2466','Culture'),

new cat('2468','2466','Language'),

new cat('2469','2466','Teacher Resources'),

new cat('2470','2466','culture'),

new cat('2471','2440','Hebrew'),

new cat('2472','2471','Culture'),

new cat('2473','2471','Language'),

new cat('2474','2471','Teacher Resources'),

new cat('2475','2471','culture'),

new cat('2476','2440','Italian'),

new cat('2477','2476','Culture'),

new cat('2478','2476','Language'),

new cat('2479','2476','Teacher Resources'),

new cat('2480','2476','culture'),

new cat('2481','2440','Japanese'),

new cat('2482','2481','Culture'),

new cat('2483','2481','Language'),

new cat('2484','2481','Teacher Resources'),

new cat('2485','2481','culture'),

new cat('2513','','Mathematics and Statistics'),

new cat('2514','2513','Mathematics'),

new cat('2515','2514','Algebra and Number Theory'),

new cat('2516','2515','Abstract Algebra'),

new cat('2517','2515','Cryptography'),

new cat('2518','2515','Linear Algebra'),

new cat('2519','2515','Number Theory'),

new cat('2560','2514','General and Liberal Arts Math'),

new cat('2561','2560','Consumer Mathematics'),

new cat('2562','2560','History of Mathematics'),

new cat('2563','2560','Mathematical Modeling'),

new cat('2564','2560','Mathematics in Art and Music'),

new cat('2565','2560','Recreational Mathematics'),

new cat('2566','2514','Geometry and Topology'),

new cat('2567','2566','Computational Geometry'),

new cat('2568','2566','Differential Geometry'),

new cat('2569','2566','Euclidean Geometry'),

new cat('2570','2566','Knot Theory'),

new cat('2571','2566','Non-Euclidean Geometry'),

new cat('2572','2566','Topology'),

new cat('2573','2566','Transformational Geometry'),

new cat('2574','2514','Mathematical Modeling'),

new cat('2575','2574','Continuous'),

new cat('2576','2574','Discrete'),

new cat('2577','2574','Mathematical Biology'),

new cat('2578','2514','Mathematics Publishing Tools'),

new cat('2579','2578','Computer Algebra Systems'),

new cat('2580','2578','HTML and MathML'),

new cat('2581','2578','Java'),

new cat('2582','2578','Non-Java Programming'),

new cat('2583','2578','Scientific Word Processors'),

new cat('2584','2578','Tex'),

new cat('2585','2578','Text Editors'),

new cat('2586','2514','Numerical Analysis'),

new cat('2587','2586','Numerical Differential Equations'),

new cat('2588','2586','Numerical Linear Algebra'),

new cat('2589','2586','Numerical Partial Differential Equations'),

new cat('2590','2586','Numerical Solution of Equations'),

new cat('2591','2514','Pre-Calculus Mathematics'),

new cat('2592','2591','Algebra'),

new cat('2593','2591','Analytic Geometry'),

new cat('2594','2591','Trigonometry'),

new cat('2520','2514','Analysis'),

new cat('2521','2520','Complex Analysis'),

new cat('2522','2520','Fourier Analysis'),

new cat('2523','2520','Functional Analysis'),

new cat('2524','2520','Real Analysis'),

new cat('2525','2520','Wavelets'),

new cat('2526','2514','Applied Mathematics'),

new cat('2527','2514','Calculus'),

new cat('2528','2527','Differential, One Variable'),

new cat('2529','2527','Integral, One Variable'),

new cat('2530','2527','Multivariable'),

new cat('2531','2527','Sequences and Series'),

new cat('2532','2514','Developmental Mathematics'),

new cat('2533','2532','Arithmetic'),

new cat('2534','2532','Geometry'),

new cat('2535','2532','Pre-Algebra'),

new cat('2536','2514','Differential Equations'),

new cat('2537','2536','Advanced'),

new cat('2538','2536','Diffusion'),

new cat('2539','2536','Elementary'),

new cat('2540','2536','Fluid Dynamics'),

new cat('2541','2536','PDE?s'),

new cat('2542','2536','Wave Phenomena'),

new cat('2543','2514','Discrete Mathematics'),

new cat('2544','2543','Cellular Automata'),

new cat('2545','2543','Combinatorics'),

new cat('2546','2543','Game Theory'),

new cat('2547','2543','Genetic Algorithms'),

new cat('2548','2543','Graph Theory'),

new cat('2549','2543','Linear Programming'),

new cat('2550','2543','Theory of Computation'),

new cat('2551','2514','Dynamical Systems'),

new cat('2552','2551','Complex Dynamics'),

new cat('2553','2551','Difference Equations'),

new cat('2554','2551','Fractal Geometry'),

new cat('2555','2551','Real Variable Dynamics'),

new cat('2556','2551','Symbolic Dynamics and Coding Theory'),

new cat('2557','2514','Foundations of Mathematics'),

new cat('2558','2557','Logic'),

new cat('2559','2557','Set Theory'),

new cat('2595','2513','Statistics and Probability'),

new cat('2596','2595','Bayesian Analysis'),

new cat('2597','2595','Brownian Motion'),

new cat('2598','2595','Data Analysis'),

new cat('2599','2595','Linear Regression'),

new cat('2600','2595','Markov Chains'),

new cat('2601','2595','Nonlinear Regression'),

new cat('2602','2595','Probability'),

new cat('2603','2595','Queueing Theory'),

new cat('2604','2595','Statistics'),

new cat('2605','','Science and Technology'),

new cat('2606','2605','Agriculture'),

new cat('2683','2605','Health Sciences'),

new cat('2684','2683','Cardiology'),

new cat('2693','2683','Geriatrics'),

new cat('2694','2683','Hematology'),

new cat('2695','2683','Internal Medicine'),

new cat('2696','2683','Medical Laboratory Technology'),

new cat('2697','2683','Nephrology'),

new cat('2698','2683','Neurology'),

new cat('2699','2683','Nursing'),

new cat('2700','2683','Nutrition'),

new cat('2701','2683','OB and Gyn'),

new cat('2702','2683','Oncology'),

new cat('2685','2683','Dentistry'),

new cat('2703','2683','Ophthalmology'),

new cat('2704','2683','Orthopedics'),

new cat('2705','2683','Other'),

new cat('2706','2683','Pathology'),

new cat('2707','2683','Pediatrics'),

new cat('2708','2683','Pharmacy'),

new cat('2709','2683','Psychiatry and Mental Health'),

new cat('2710','2683','Public Health'),

new cat('2711','2683','Pulmonary Medicine'),

new cat('2712','2683','Radiology'),

new cat('2686','2683','Dermatology'),

new cat('2713','2683','Rheumatology'),

new cat('2714','2683','Sports Medicine'),

new cat('2715','2683','Surgery'),

new cat('2716','2683','Therapeutics'),

new cat('2687','2683','Ear, Nose, and Throat'),

new cat('2688','2683','Endocrinology'),

new cat('2689','2683','Epidemiology'),

new cat('2690','2683','Gastroenterology'),

new cat('2691','2683','General'),

new cat('2692','2683','Genetics and Infectious Disease'),

new cat('2717','2605','Information Technology'),

new cat('2718','2717','Applications'),

new cat('2727','2717','Security'),

new cat('2728','2717','Software Engineering'),

new cat('2729','2717','Systems Analysis'),

new cat('2730','2717','Web'),

new cat('2719','2717','Computer Information Systems'),

new cat('2720','2717','Database'),

new cat('2721','2717','E-commerce'),

new cat('2722','2717','Hardware'),

new cat('2723','2717','Information Literacy'),

new cat('2724','2717','Networking'),

new cat('2725','2717','Operating Systems'),

new cat('2726','2717','Programming'),

new cat('2731','2605','Nanotechnology'),

new cat('2732','2731','Computation and Software'),

new cat('2733','2731','Nano-bio Device and Systems'),

new cat('2734','2731','Nano-electromechanical Systems (NEMS)'),

new cat('2735','2731','Nano-electronics'),

new cat('2736','2605','Physics'),

new cat('2737','2736','Classical Mechanics'),

new cat('2738','2737','1D Kinematics'),

new cat('2747','2737','Reference Frames'),

new cat('2748','2737','Statics'),

new cat('2739','2737','2D Kinematics'),

new cat('2740','2737','Angular Motion and Torques'),

new cat('2741','2737','Energy and Momentum'),

new cat('2742','2737','Forces and Dynamics'),

new cat('2743','2737','Gravity'),

new cat('2744','2737','Linear Motion, Forces, and Equilibrium'),

new cat('2745','2737','Projectile Motion'),

new cat('2746','2737','Pulleys and Atwood Machines'),

new cat('2749','2736','Electricity and Magnetism'),

new cat('2750','2749','Capacitance'),

new cat('2751','2749','Charges in Fields'),

new cat('2752','2749','Circuits'),

new cat('2753','2749','Electric Fields'),

new cat('2754','2749','Electric Potentials'),

new cat('2755','2749','Faradays Law and Induction'),

new cat('2756','2749','Gauss and Coulombs Law'),

new cat('2757','2749','Magnetic Fields'),

new cat('2758','2736','General'),

new cat('2759','2758','Collections'),

new cat('2760','2758','Curriculum'),

new cat('2761','2758','History'),

new cat('2762','2758','Mathematics'),

new cat('2763','2758','Measurement and Units'),

new cat('2764','2758','Physics Education Research'),

new cat('2765','2758','Reference'),

new cat('2766','2758','Vector Algebra'),

new cat('2767','2736','Modern Physics'),

new cat('2768','2767','Atomic and Molecular'),

new cat('2769','2767','Condensed Matter'),

new cat('2770','2767','General'),

new cat('2771','2767','Laser Physics'),

new cat('2772','2767','Nuclear and Particle'),

new cat('2773','2767','Relativity'),

new cat('2774','2736','Optics'),

new cat('2775','2774','Geometric Optics'),

new cat('2776','2774','Physical Optics'),

new cat('2777','2774','Radiation'),

new cat('2778','2736','Oscillations and Waves'),

new cat('2779','2778','Oscillators'),

new cat('2780','2778','Pendulum Motion'),

new cat('2781','2778','Waves'),

new cat('2782','2736','Quantum Mechanics'),

new cat('2783','2736','Thermodynamics and Statistical Mechanics'),

new cat('2784','2783','Fluids'),

new cat('2785','2783','Statistical Mechanics'),

new cat('2786','2783','Thermodynamics'),

new cat('2607','2605','Astronomy'),

new cat('2608','2605','Biology'),

new cat('2609','2608','Botany'),

new cat('2618','2608','Microbiology'),

new cat('2619','2608','Molecular Biology'),

new cat('2620','2608','Natural History'),

new cat('2621','2608','Physiology'),

new cat('2622','2608','Zoology'),

new cat('2610','2608','Cytology'),

new cat('2611','2608','Development'),

new cat('2612','2608','Ecology'),

new cat('2613','2608','Evolution'),

new cat('2614','2608','General'),

new cat('2615','2608','Genetics'),

new cat('2616','2608','Human Anatomy'),

new cat('2617','2608','Life'),

new cat('2623','2605','Chemistry'),

new cat('2624','2623','Analytical'),

new cat('2633','2623','Physical'),

new cat('2634','2623','Polymer and Macromolecular'),

new cat('2625','2623','Biochemistry'),

new cat('2626','2623','Chemical Education'),

new cat('2627','2623','Environmental'),

new cat('2628','2623','Inorganic'),

new cat('2629','2623','Introductory and General'),

new cat('2630','2623','Materials'),

new cat('2631','2623','Nuclear'),

new cat('2632','2623','Organic'),

new cat('2635','2605','Computer Science'),

new cat('2636','2635','Artificial Intelligence'),

new cat('2645','2635','Programming'),

new cat('2646','2635','Programming Languages'),

new cat('2647','2635','Robotics'),

new cat('2648','2635','Study and Teaching'),

new cat('2649','2635','Theory'),

new cat('2650','2635','User Interfaces'),

new cat('2637','2635','Computer Simulation'),

new cat('2638','2635','Computer Software'),

new cat('2639','2635','Databases'),

new cat('2640','2635','Documentation'),

new cat('2641','2635','General'),

new cat('2642','2635','Human-Computer Interaction'),

new cat('2643','2635','Internet'),

new cat('2644','2635','Multimedia'),

new cat('2651','2605','Engineering'),

new cat('2652','2651','Aerospace and Aeronautical Engineering'),

new cat('2661','2651','General'),

new cat('2662','2651','Geological Engineering'),

new cat('2663','2651','Industrial and Systems'),

new cat('2664','2651','Manufacturing Engineering'),

new cat('2665','2651','Materials Science and Engineering'),

new cat('2666','2651','Mechanical Engineering'),

new cat('2667','2651','Mining Engineering'),

new cat('2668','2651','Nuclear Engineering'),

new cat('2669','2651','Ocean Engineering'),

new cat('2670','2651','Petroleum Engineering'),

new cat('2653','2651','Agricultural and Biological Engineering'),

new cat('2654','2651','Biomedical Engineering'),

new cat('2655','2651','Chemical Engineering'),

new cat('2656','2651','Civil Engineering'),

new cat('2657','2651','Computer Engineering'),

new cat('2658','2651','Electrical Engineering'),

new cat('2659','2651','Engineering Science'),

new cat('2660','2651','Environmental Engineering'),

new cat('2671','2605','Fire Safety'),

new cat('2672','2671','Building Construction for Fire Protection'),

new cat('2673','2671','Disaster Planning'),

new cat('2674','2671','Fire Administration'),

new cat('2675','2671','Fire Behavior and Combustion'),

new cat('2676','2671','Fire Investigation'),

new cat('2677','2671','Fire Prevention'),

new cat('2678','2671','Fire Protection Systems'),

new cat('2679','2671','Foundations of Emergency Services'),

new cat('2680','2671','Hazardous Materials'),

new cat('2681','2605','General Science'),

new cat('2682','2605','Geology'),

new cat('2787','','Social Sciences'),

new cat('2788','2787','Anthropology'),

new cat('2827','2787','Statistics'),

new cat('2828','2787','Womens Studies'),

new cat('2789','2787','Criminal Justice'),

new cat('2790','2789','Corrections'),

new cat('2800','2789','Law Enforcement'),

new cat('2799','2789','Law \&amp; Society'),

new cat('2801','2789','Victims'),

new cat('2791','2789','Courts'),

new cat('2792','2789','Crime'),

new cat('2793','2789','Crime and Communites'),

new cat('2794','2789','Drugs'),

new cat('2795','2789','Forensics'),

new cat('2796','2789','Intelligence'),

new cat('2797','2789','Justice System'),

new cat('2798','2789','Juvenile Justice'),

new cat('2802','2787','General'),

new cat('2803','2787','Geography'),

new cat('2804','2787','Law'),

new cat('2805','2787','Political Science'),

new cat('2806','2787','Psychology'),

new cat('2807','2806','Biological'),

new cat('2819','2806','Industrial and Organizational'),

new cat('2820','2806','Learning and Memory'),

new cat('2821','2806','Personality'),

new cat('2822','2806','Sensation and Perception'),

new cat('2823','2806','Social'),

new cat('2824','2806','Statistics and Research Methods'),

new cat('2808','2806','Clinical and Counseling'),

new cat('2809','2808','Abnormal and Psychopathology'),

new cat('2810','2808','Assessment'),

new cat('2811','2808','Therapy and Treatment'),

new cat('2812','2806','Cognitive'),

new cat('2813','2806','Community and Health'),

new cat('2814','2806','Developmental and Life-Span'),

new cat('2815','2806','Diversity'),

new cat('2816','2806','Ethics'),

new cat('2817','2806','General'),

new cat('2818','2806','History and Systems'),

new cat('2825','2787','Sociology'),

new cat('2826','2787','Sports and Games')

);

function subcat(value,field) {
 subcatID(value, field, 0);
}

function subcatID(value,field,show_id) {
 var selectedValue;
 var pathList="<B>Selected:</B> ";
 var el = document.getElementById('catPath');
 
 var j=1;
 var tempId;
 var selectedCat;
 var childrenNameStack= new Array();
 var childrenIdStack = new Array();
 var pathNameStack = new Array();
 var pathIdStack = new Array();

 if (value == "-1") selectedValue = document.merlotForm.elements[field].options[document.merlotForm.elements[field].selectedIndex].value;
 else selectedValue = value;
   
 for (i=0; i<=cats.length-1; i++) if(cats[i].id == selectedValue) selectedCat = cats[i];
 tempId = selectedCat.parentId;

 for (i=cats.length-1;i>=0 ;i--) {
    if (cats[i].id == tempId) {
	   pathNameStack.push(cats[i].name);
	   pathIdStack.push(cats[i].id);
	   tempId = cats[i].parentId;
	}
	if (cats[i].parentId == selectedValue) {
	   childrenNameStack.push(cats[i].name);
	   childrenIdStack.push(cats[i].id);
	   tempId = cats[i].parentId;
	}
 }

 document.merlotForm.elements[field].options.length = 0;
 if (selectedCat.id > 0){
    if (childrenNameStack.length > 0) document.merlotForm.elements[field].options[0]= new Option('See sub-categories in ' + selectedCat.name +"...", selectedCat.id);
	else document.merlotForm.elements[field].options[0]= new Option(selectedCat.name, selectedCat.id);
 }
 else document.merlotForm.elements[field].options[0]= new Option(selectedCat.name, selectedCat.id);
 if (selectedValue !="")
 {
	   document.merlotForm.elements[field].options[1]= new Option('--UP--', selectedCat.parentId);
	   j++;
 }
 for (i=childrenNameStack.length-1; i>=0; i--){
    document.merlotForm.elements[field].options[j]= new Option(childrenNameStack[i], childrenIdStack[i]);
    j++
 }
 if (selectedValue == "" && cats[0].name != "All");
 else pathList = pathList + "<a href=\"javascript:subcat('', '" + field + "')\">All</a>";
 for (i=pathNameStack.length-2; i>=0; i--) pathList = pathList + " &gt; <a href=\"javascript:subcat('" + pathIdStack[i] + "', '" + field + "')\">" + pathNameStack[i] +"</a>";
    if (selectedCat.id != pathIdStack[0]) pathList = pathList + " &gt; <a href=\"javascript:subcat('" + selectedCat.id + "', '" + field + "')\">" + selectedCat.name +"</a>";
    if (show_id == 1) pathList = pathList + "&nbsp; (" + selectedCat.id + ")";
     el.innerHTML = pathList;
}

function defaultCat(id, field) {
subcat(id, field);
}

function defaultCatWithId(id, field) {
subcatID(id, field, 1);
}

var Today = new Date();

var MonthDays = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
var MonthNames = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
var curYear = Today.getFullYear();
var curMonth = Today.getMonth() + 1;
var curDay = Today.getDate();
var monthFlag = "false";
var dayFlag = "false";
var yearFlag = "false";
var time = Today.getTime();
var yearsBack = 60;
var yearsAhead = 5;

function setCurrent(field, form, el)
{
    monthFieldName = "month"+ field;
    dayFieldName = "day"+ field;
    yearFieldName = "year"+ field;

	if(el.checked==true){
	if (document.forms[form].elements[monthFieldName]) document.forms[form].elements[monthFieldName].disabled=true;
    if (document.forms[form].elements[dayFieldName]) document.forms[form].elements[dayFieldName].disabled=true;
	if (document.forms[form].elements[yearFieldName]) document.forms[form].elements[yearFieldName].disabled=true;
	document.forms[form].elements[field].value="";
	}
	else{
    if (document.forms[form].elements[monthFieldName]) document.forms[form].elements[monthFieldName].disabled=false;
    if (document.forms[form].elements[dayFieldName]) document.forms[form].elements[dayFieldName].disabled=false;
	if (document.forms[form].elements[yearFieldName]) document.forms[form].elements[yearFieldName].disabled=false;
	
	hiddenDate = "";
    if (document.forms[form].elements[yearFieldName]) hiddenDate = hiddenDate + document.forms[formName].elements[yearFieldName].options[document.forms[formName].elements[yearFieldName].selectedIndex].value;
    else hiddenDate = hiddenDate + "9999";

	hiddenDate = hiddenDate + "-";

	if (document.forms[form].elements[monthFieldName]){
	monthIndex= document.forms[formName].elements[monthFieldName].selectedIndex + 1;
	hiddenDate = hiddenDate + monthIndex;
	}
	else hiddenDate = hiddenDate + "01";
	
	hiddenDate = hiddenDate + "-";

    if (document.forms[form].elements[dayFieldName]) hiddenDate = hiddenDate + document.forms[formName].elements[dayFieldName].options[document.forms[formName].elements[dayFieldName].selectedIndex].value;
    else hiddenDate = hiddenDate + "01";

	document.forms[form].elements[field].value= hiddenDate;
	}
}

function setupDatePulldowns(field, form, inputDate, format, currentFlag, id)
{

formName = form;
dateFieldFormat = format;

monthFieldName = "month"+ field;
dayFieldName = "day"+ field;
yearFieldName = "year"+ field;
currentFieldName = "current"+ field;


if (format.indexOf('{M}') >= 0) monthFlag = "true";
if (format.indexOf('{D}') >= 0) dayFlag = "true";
if (format.indexOf('{Y}') >= 0) yearFlag = "true";

if (inputDate=="")
{

   if (monthFlag == "true") selectedMonth = curMonth;
   if (dayFlag == "true") selectedDay = curDay;
   if (yearFlag == "true") selectedYear = curYear;  
}
else
{
selectedMonth = inputDate.substring(5,7);
selectedDay = inputDate.substring(8,10);
selectedYear = inputDate.substring(0,4);
}

monthField = "<select name='" + monthFieldName + "' onChange=\"update('" + field + "');\"></select>";
dayField = "<select name='" + dayFieldName + "' onChange=\"update('" + field + "');\"></select>";
yearField = "<select name='" + yearFieldName + "' onChange=\"update('" + field + "');\"></select>";
currentField = " <INPUT TYPE='checkbox' NAME='" + currentFieldName + "' onClick=\"setCurrent('" + field + "','" + formName + "',this)\">Current";

if (monthFlag == "true") dateFieldFormat = dateFieldFormat.replace("{M}",monthField);
if (dayFlag == "true") dateFieldFormat = dateFieldFormat.replace("{D}",dayField);
if (yearFlag == "true") dateFieldFormat = dateFieldFormat.replace("{Y}",yearField);
if (currentFlag == "true") dateFieldFormat = dateFieldFormat + currentField;

document.writeln(dateFieldFormat + "<INPUT TYPE='hidden' NAME='" + field + "'>");

if (monthFlag == "true")
{
  for (i=0;i<=11;i++) document.forms[formName].elements[monthFieldName].options[i] = new Option(MonthNames[i],i+1);
  
  if (selectedMonth>0) document.forms[formName].elements[monthFieldName].selectedIndex = selectedMonth-1;
  else document.forms[formName].elements[monthFieldName].selectedIndex = 0;
}

if (dayFlag == "true")
{
   if (selectedMonth==2)
   {
      if(selectedYear%4 == 0) pdEndDate = 29;
      else pdEndDate = 28;
   }
   else pdEndDate = MonthDays[document.forms[formName].elements[monthFieldName].selectedIndex];

   for (i=0; i<= pdEndDate-1; i++)document.forms[formName].elements[dayFieldName].options[i] = new Option(i+1,i+1);
   document.forms[formName].elements[dayFieldName].selectedIndex = selectedDay-1;
}

if (yearFlag == "true")
{
   for (i=0;i<=(yearsBack+yearsAhead);i++)document.forms[formName].elements[yearFieldName].options[i] = new Option(curYear-i+yearsAhead,curYear-i+yearsAhead);
   document.forms[formName].elements[yearFieldName].selectedIndex = curYear-selectedYear+yearsAhead;
}

hiddenDate = "";

if (yearFlag == "true") hiddenDate = hiddenDate + document.forms[formName].elements[yearFieldName].options[document.forms[formName].elements[yearFieldName].selectedIndex].value;
else hiddenDate = hiddenDate + "1999";
hiddenDate = hiddenDate + "-";

if (monthFlag == "true")
	{
	monthIndex= document.forms[formName].elements[monthFieldName].selectedIndex + 1;
	hiddenDate = hiddenDate + monthIndex;
	}
else hiddenDate = hiddenDate + "01";
hiddenDate = hiddenDate + "-";

if (dayFlag == "true") hiddenDate = hiddenDate + document.forms[formName].elements[dayFieldName].options[document.forms[formName].elements[dayFieldName].selectedIndex].value;
else hiddenDate = hiddenDate + "01";

document.forms[formName].elements[field].value= hiddenDate;

if (currentFlag=="true" && inputDate=="" && id !="")
{
    document.forms[formName].elements[currentFieldName].checked=true;
	setCurrent(field, formName, document.forms[formName].elements[currentFieldName]);
}

}

function update(fieldName)
{
monthFieldName = "month"+ fieldName;
dayFieldName = "day"+ fieldName;
yearFieldName = "year"+ fieldName;

   if (monthFlag == "true") selectedMonth = document.forms[formName].elements[monthFieldName].selectedIndex + 1;
   else selectedMonth = 1;
   if (yearFlag == "true") selectedYear = document.forms[formName].elements[yearFieldName].options[document.forms[formName].elements[yearFieldName].selectedIndex].value; 
   else selectedYear = 1900;
     
if (dayFlag == "true")
{   
   selectedDay = document.forms[formName].elements[dayFieldName].selectedIndex;
   
   for (i=0;i<= document.forms[formName].elements[dayFieldName].length;i++) document.forms[formName].elements[dayFieldName].options[i] = null;
   
   if (selectedMonth==2)
   {
      if(selectedYear%4 == 0) pdEndDate = 29;
      else pdEndDate = 28;
   }
   else pdEndDate = MonthDays[document.forms[formName].elements[monthFieldName].selectedIndex];
   
   if (selectedDay > MonthDays[document.forms[formName].elements[monthFieldName].selectedIndex]) selectedDay = MonthDays[document.forms[formName].elements[monthFieldName].selectedIndex];

   for (i=0; i<= pdEndDate-1; i++) document.forms[formName].elements[dayFieldName].options[i] = new Option(i+1,i+1);
   if (selectedDay > document.forms[formName].elements[dayFieldName].length -1) selectedDay = document.forms[formName].elements[dayFieldName].length -1;
   document.forms[formName].elements[dayFieldName].selectedIndex = selectedDay;
}
hiddenDate = "";

if (yearFlag == "true") hiddenDate = hiddenDate + document.forms[formName].elements[yearFieldName].options[document.forms[formName].elements[yearFieldName].selectedIndex].value;
else hiddenDate = hiddenDate + "1999";
hiddenDate = hiddenDate + "-";

if (monthFlag == "true")
	{
	monthIndex= document.forms[formName].elements[monthFieldName].selectedIndex + 1;
	hiddenDate = hiddenDate + monthIndex;
	}
else hiddenDate = hiddenDate + "01";
hiddenDate = hiddenDate + "-";

if (dayFlag == "true") hiddenDate = hiddenDate + document.forms[formName].elements[dayFieldName].options[document.forms[formName].elements[dayFieldName].selectedIndex].value;
else hiddenDate = hiddenDate + "01";

document.forms[formName].elements[fieldName].value= hiddenDate;
}



