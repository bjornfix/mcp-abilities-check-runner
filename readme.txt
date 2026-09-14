=== MCP Abilities - Check Runner ===
Contributors: basicus
Tags: mcp, abilities, plugin-check
Requires at least: 6.9
Tested up to: 7.1
Requires PHP: 8.0
Requires Plugins: plugin-check
Stable tag: 0.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

MCP bridge for the official WordPress.org Plugin Check plugin.

== Description ==

Download the stable plugin ZIP from https://downloads.devenia.com/mcp-abilities-check-runner.zip.

Check Runner connects an authenticated MCP client to official WordPress Plugin Check. It inspects an installed plugin and returns error and warning counts with structured findings.

`plugin-check/run` runs synchronously by default. With `async: true`, it schedules a WordPress background job and returns a reference. Use `plugin-check/job-status` to read that job's state and final result. Scheduling success is not an inspection pass; a completed job can contain findings or an execution failure.

The inspection always requests all available checks, including experimental checks. Requests to narrow checks or categories are ignored. The installed Plugin Check version determines the available checks. A pass requires zero errors and zero warnings; it does not prove functional correctness or guarantee WordPress.org approval.

The response lists at most 100 findings by default, or up to 500 with `max_results`. Errors come before warnings. `truncated: true` means the displayed list is incomplete; counts cover the inspection result. There is no pagination. Use the official Plugin Check interface to inspect further details.

The run action requires the WordPress permission to activate plugins. Job status requires the permission to manage site options. Background execution depends on WordPress cron and server resources. This version has no job cancellation or deletion action.

The official [Plugin Check](https://wordpress.org/plugins/plugin-check/) plugin must be installed and active. WordPress 6.9 includes the Abilities API. Configure an authenticated MCP Adapter connection and verify that both abilities are discoverable before starting a background job. Depending on configuration, an exposure layer may be needed for the run action.

Check Runner does not modify source files, install candidates or publish releases. Plugin Check owns the inspection and can exercise WordPress behaviour during checks; use a suitable test site.

Read the [product page](https://devenia.com/plugins/mcp-abilities-check-runner/) for the workflow and requirements.

== Changelog ==

= 0.2.4 =
* Declare the official Plugin Check dependency and clarify WordPress core Abilities API requirements.
* Explain inspection scope, background jobs and response limits.

= 0.2.3 =
* Preserve the server-configured PHP execution budget instead of mutating runtime limits from plugin code.

= 0.2.2 =
* Keep asynchronous checks alive for a bounded five-minute window and persist a terminal failure receipt if the request stops unexpectedly.

= 0.2.1 =

* Uses WordPress's scoped admin-memory filter instead of direct PHP runtime-limit mutations during complete checks.

= 0.2.0 =

* Adds a server-owned asynchronous Plugin Check job and status interface so complete official checks can finish beyond HTTP gateway timeouts without skipping checks.

= 0.1.4 =

* Raises the bounded analysis ceiling for large plugins to 1 GB and five minutes, then restores request limits.

= 0.1.3 =

* Gives the bounded Plugin Check/PHPCS run a 512 MB analysis allowance and restores the request memory limit afterward.

= 0.1.2 =

* Scopes Devenia source-design gate bypass to Plugin Check's own disposable publication fixtures so the official runner can complete without weakening normal publication requests.

= 0.1.1 =
* Always run all Plugin Check checks, including experimental checks.
* Ignore check/category filters so callers cannot accidentally skip checks.
* Treat warnings as failed gate results.

= 0.1.0 =
* Initial MCP bridge for WordPress.org Plugin Check.
