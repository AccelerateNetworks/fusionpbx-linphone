<?php
	//application details
		$apps[$x]['name'] = "Linphone";
		$apps[$x]['uuid'] = "873ee273-e675-4a41-9fc6-3653ce97e9e7";
		$apps[$x]['category'] = "Vendor";
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "1.0";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "";
		$apps[$x]['description']['en-gb'] = "";
		$apps[$x]['description']['ar-eg'] = "";
		$apps[$x]['description']['de-at'] = "";
		$apps[$x]['description']['de-ch'] = "";
		$apps[$x]['description']['de-de'] = "";
		$apps[$x]['description']['es-cl'] = "";
		$apps[$x]['description']['es-mx'] = "";
		$apps[$x]['description']['fr-ca'] = "";
		$apps[$x]['description']['fr-fr'] = "";
		$apps[$x]['description']['he-il'] = "";
		$apps[$x]['description']['it-it'] = "";
		$apps[$x]['description']['nl-nl'] = "";
		$apps[$x]['description']['pl-pl'] = "";
		$apps[$x]['description']['pt-br'] = "";
		$apps[$x]['description']['pt-pt'] = "";
		$apps[$x]['description']['ro-ro'] = "";
		$apps[$x]['description']['ru-ru'] = "";
		$apps[$x]['description']['sv-se'] = "";
		$apps[$x]['description']['uk-ua'] = "";

	//default settings
		$y=0;
		$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "954b33bd-141b-4101-bdf7-17d3dcec542f";
		$apps[$x]['default_settings'][$y]['default_setting_category'] = "provision";
		$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "linphone_version_check_url_root";
		$apps[$x]['default_settings'][$y]['default_setting_name'] = "text";
		$apps[$x]['default_settings'][$y]['default_setting_value'] = "https://www.linphone.org/releases";
		$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "true";
		$apps[$x]['default_settings'][$y]['default_setting_description'] = "URL to check for updates";
		$y++;


		$y = 0;
		$z = 0;
		$apps[$x]['db'][$y]['table']['name'] = "linphone_devices";
		$apps[$x]['db'][$y]['table']['parent'] = "";

		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'device_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'identification token for provisioning';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'name';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'name of the linphone device';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'provisioning_secret';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'secret for provisioning';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_domains';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = "extension_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_extensions";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "extension_uuid";
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = "user_agent";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'upload_secret';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '32-char lowercase hex (16 random bytes); per-device auth for the RCS HTTP file-transfer upload endpoint. NOT NULL / UNIQUE / format CHECK constraints not declarable in the fusionpbx schema dsl; applied by the migration block at the end of this file.';
		$z++;

		$y++;
		$z=0;
		$apps[$x]['db'][$y]['table']['name'] = "linphone_profiles";
		$apps[$x]['db'][$y]['table']['parent'] = "";

		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'profile_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_domains';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = "name";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
		$z++;

		$y++;
		$z=0;
		$apps[$x]['db'][$y]['table']['name'] = "linphone_profile_settings";
		$apps[$x]['db'][$y]['table']['parent'] = "";

		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'profile_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_domains';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = "setting";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = "value";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
		$z++;

		$y++;
		$z=0;
		$apps[$x]['db'][$y]['table']['name'] = "linphone_profile_devices";
		$apps[$x]['db'][$y]['table']['parent'] = "";

		$apps[$x]['db'][$y]['fields'][$z]['name'] = "device_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "linphone_devices";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "device_uuid";
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_domains';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;

		$apps[$x]['db'][$y]['fields'][$z]['name'] = "profile";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "linphone_profiles";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "profile_uuid";
		$z++;

		$y=0;
		$z=0;
		$apps[$x]['permissions'][$y]['name'] = "linphone_manage_domain";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;

		$apps[$x]['permissions'][$y]['name'] = "linphone_manage_all";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;

		$apps[$x]['permissions'][$y]['name'] = "linphone_manage_self";
		$apps[$x]['permissions'][$y]['groups'][] = "user";
		$y++;

	//phase-2 schema constraints not declarable in fusionpbx schema dsl;
	//apply post-CREATE TABLE during any upgrade flow that loads this file.
	//idempotent; single transaction; gated per-phase so each step only runs when needed.
	//pgsql-specific (pg_constraint catalog, regex operator in CHECK, gen_random_bytes).
		$notnull_applied = $database->select(
			"SELECT 1 FROM information_schema.columns
			 WHERE table_name = 'linphone_devices'
			   AND column_name = 'upload_secret'
			   AND is_nullable = 'NO'",
			[], 'column'
		);
		$unique_applied = $database->select(
			"SELECT 1 FROM pg_constraint WHERE conname = 'linphone_devices_upload_secret_unique'",
			[], 'column'
		);
		$format_applied = $database->select(
			"SELECT 1 FROM pg_constraint WHERE conname = 'linphone_devices_upload_secret_format'",
			[], 'column'
		);

		if (!$notnull_applied || !$unique_applied || !$format_applied) {
			echo "linphone: applying upload_secret schema migration (one-time post-install)...<br/>";
			try {
				$database->execute("BEGIN");

				// Phase 1: column add + populate + NOT NULL
				// Gated on NOT NULL absence (covers: column missing OR column nullable).
				// pgcrypto needed only for gen_random_bytes in the UPDATE; co-gated.
				if (!$notnull_applied) {
					$database->execute("CREATE EXTENSION IF NOT EXISTS pgcrypto");
					$database->execute("ALTER TABLE linphone_devices ADD COLUMN IF NOT EXISTS upload_secret TEXT");
					$database->execute("UPDATE linphone_devices SET upload_secret = encode(gen_random_bytes(16), 'hex') WHERE upload_secret IS NULL");
					$database->execute("ALTER TABLE linphone_devices ALTER COLUMN upload_secret SET NOT NULL");
				}

				// Phase 2: UNIQUE constraint
				if (!$unique_applied) {
					$database->execute("ALTER TABLE linphone_devices ADD CONSTRAINT linphone_devices_upload_secret_unique UNIQUE (upload_secret)");
				}

				// Phase 3: format CHECK constraint
				if (!$format_applied) {
					$database->execute("ALTER TABLE linphone_devices ADD CONSTRAINT linphone_devices_upload_secret_format CHECK (upload_secret ~ '^[a-f0-9]{32}\$')");
				}

				$database->execute("COMMIT");
				echo "linphone: upload_secret schema migration applied.<br/>";
			} catch (Throwable $e) {
				$database->execute("ROLLBACK");
				echo "linphone: upload_secret schema migration FAILED: " . htmlspecialchars($e->getMessage()) . "<br/>";
				echo "linphone: apply manually inside a single transaction: <code>BEGIN; CREATE EXTENSION IF NOT EXISTS pgcrypto; ALTER TABLE linphone_devices ADD COLUMN IF NOT EXISTS upload_secret TEXT; UPDATE linphone_devices SET upload_secret = encode(gen_random_bytes(16), 'hex') WHERE upload_secret IS NULL; ALTER TABLE linphone_devices ALTER COLUMN upload_secret SET NOT NULL; ALTER TABLE linphone_devices ADD CONSTRAINT linphone_devices_upload_secret_unique UNIQUE (upload_secret); ALTER TABLE linphone_devices ADD CONSTRAINT linphone_devices_upload_secret_format CHECK (upload_secret ~ '^[a-f0-9]{32}\$'); COMMIT;</code><br/>";
			}
		}
