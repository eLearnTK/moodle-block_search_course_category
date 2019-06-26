# course_category_finder

Course Category Finder

Blockbeschreibung:

Der „Course Category Finder“ ist eine erweiterung des Blocks „Course Finder“ von Azmat Ullah und Talha Noor. Zusätzlich, zu der Suche nach Kursnamen, ist es mit dem Course Category Finder dem Nutzer möglich auch nach Kategorien zu suchen, denen Kurse zugeordnet wurden. 
Über eine neu hinzugefügte Combo-Box lässt sich die Option für die Suche auswählen. Über einen Button wird die Suche gestartet. 
Das gelieferte Ergebnis ist eine Auflistung aller Kurse, die dem Suchbegriff entsprechen. Sollte bei der Suche nach Kategorien kein Treffer erzielt werden, so wird auf die Index-Seite der vorhandenen Kategorien verwiesen.


Aufbau des Blocks:

Der Block besteht aus den folgenden Teilkomponenten: 

db/access.php
 
Hier werden die Befähigungen des Blocks gesetzt. 

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'block/search_course_category:addinstance' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
);

lang/de/block_search_course_category.php
lang/de/block_search_course_category.php

Diese Dateien enthalten Sprachoptionen in Deutsch und englisch. 

$string['pluginname'] = 'Kurs Kategorie Suche';
$string['search_course_category'] = 'Kurs Kategorie Suche';





version.php

In dieser Datei ist die aktualle Versionsnummer des Blockes enthalten, als auch die  benötigte version von Moodle. 

$plugin->version   = 2016120800;        
$plugin->requires  = 2010112400;        
$plugin->release = '3.0';
$plugin->component = 'block_search_course_category';


block_search_course_category.php

In dieser Datei wird das Layout und der Titel für den Block festgelegt. Der Block besteht aus einem Texttfeld, in dem der Suchbegriff eingegeben werden kann, einer Combo-Box in der die Suchoption(Category, Course) gewählt werden kann und einem Button um die Suche zu starten.
Beim Betätigen des Buttons wird auf die Datei search_course_category.php verwiesen, die dann den Suchbegriff verarbeitet.


search_course_category.php

Diese Datei enthält die Logik, die anhand des Suchbegriffs innerhalb der Datenbank eine Suche durchführt und das Ergebnis auflistet. 
Die Variable $option enthält die gewählte Suchoption, die entweder 'course' oder 'category' enthalten kann. In $search befindet sich der Suchbegriff.

$option = $_POST['combo_option'];
$search = optional_param('search', '', PARAM_RAW); 

Die Suche nach Kursen entspricht dem gleichen Verfahren wie sie in der standardmäßigen search.php von moodle vorhanden ist und wurde um die Option der Suche nach Kategorien erweitert. Da die standardmäßige search.php nicht geändert werden darf, damit sich die Moodle-Version nicht unterscheidet, musste die neue search_course_category.php erstellt werden.

Über eine Bedingung wird zunächst geprüft, um welche Art von Suche es sich handelt. Wenn es die neue Suche nach einer Kategorie ist, so wird zunächst eine Datenbankabfrage innerhalb der Tabelle  course_categories gestartet. Alle Suchergebnisse werden in $records aufgenommen:

$select = "name LIKE '%".$search."%'"; 
$table  = 'course_categories';
$records = $DB->get_records_select($table,$select);

Danach wird das Seitenlayout festgelegt. Dazuwird der courserenderer verwendet:

$PAGE->set_pagelayout('coursecategory');
$courserenderer = $PAGE->get_renderer('core', 'course');


Als nächstes wird geprüft, ob der Nutzer eingeloggt ist. Sollte er nicht eingeloggt sein, so hat er keinen Zugang zu den Kursen.

if ($CFG->forcelogin) 
		require_login();


Verborgene Kategorieren sind ohne Berechtigung nicht sichtbar:

if ($categoryid && 
   !$category->visible && 
   !has_capability('moodle/category:viewhiddencategories', 
                    $PAGE->context)) {
		throw new moodle_exception('unknowncategory');
}

Über eine Schleife werden alle gefunden Kategorien aufgelistet. Zur Anzeige dient der courserenderer, an den die id der Kategorie übergeben wird.

foreach($records as $record){
		$content = $courserenderer->course_category($record->id);
		echo $content;
}

Schließlich werden noch die Namen der gefundenen Kategorien aufgelistet:

echo "</br><b>Found Categories:</b></br>";
foreach($records as $record){
		echo $record->name;
		echo "</br>";
}

Dieser Codeabschnitt dient dazu zu Prüfen, ob der Nutzer die Berechtigung versteckte Kategorieen zu sehen. (Datei search_course_category.php Zeile 130). Er wurde entfernt, damit auch nicht eingeloggte Nutzer nach allen Kategorien suchen könnnen, da sonst eine Exception geworfen werden würde.

if (!has_capability('moodle/category:viewhiddencategories', $PAGE->context)) {
	throw new moodle_exception('unknowncategory');
}


