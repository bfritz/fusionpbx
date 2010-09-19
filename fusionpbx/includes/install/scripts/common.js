// Return the absolute path to custom sound based on language, dialect
// and voice settings in the FreeSWITCH session.
function find_custom_sound(session, sounds_dir, filename) {
	var default_language = session.getVariable("default_language") || "en";
	var default_dialect = session.getVariable("default_dialect") || "us";
	var default_voice = session.getVariable("default_voice") || "callie";

	return
		sounds_dir
		+ "/" + default_language
		+ "/" + default_dialect
		+ "/" + default_voice
		+ "/custom/"
		+ filename;
}
