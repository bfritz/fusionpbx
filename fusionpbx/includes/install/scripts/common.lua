-- Return the absolute path to custom sound based on language, dialect
-- and voice settings in the FreeSWITCH session.
function find_custom_sound(session, sounds_dir, filename)
    local default_language = session:getVariable("default_language") or "en"
    local default_dialect = session:getVariable("default_dialect") or "us"
    local default_voice = session:getVariable("default_voice") or "callie"

    return sounds_dir
        .. "/" .. default_language
        .. "/" .. default_dialect
        .. "/" .. default_voice
        .. "/custom/"
        .. filename;
end
