# MCP Abilities – Check Runner

Run official WordPress Plugin Check from an authenticated MCP client and see whether an installed plugin finishes with zero errors and zero warnings. A scheduled background job is only a receipt; read its final result before making a release decision.

[![Stable download](https://img.shields.io/badge/stable-0.2.4-blue)](https://downloads.devenia.com/mcp-abilities-check-runner.zip)
[![License](https://img.shields.io/badge/license-GPLv2%2B-blue)](https://www.gnu.org/licenses/gpl-2.0.html)
[![WordPress](https://img.shields.io/badge/WordPress-6.9%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple)](https://www.php.net/)

**Tested up to:** 7.1

**Stable tag:** 0.2.4

**Tags:** mcp, abilities, plugin-check

**License:** GPLv2 or later

## What It Does

Check Runner wraps the official Plugin Check runner. It checks an installed plugin, includes experimental checks and returns counts plus structured findings. A result passes only when both error and warning counts are zero.

Checks and category filters are ignored. The installed version of Plugin Check determines which checks are available. A pass does not prove that your plugin's features work or guarantee acceptance by WordPress.org.

## The Real Workflow

1. Install your candidate on a suitable test WordPress site with official Plugin Check active.
2. Identify the candidate by plugin slug or basename and choose `new` or `update` mode.
3. Run the inspection synchronously, or request `async: true` and keep the returned job reference.
4. For a background job, read the same reference until a terminal result is available. Investigate stalled or failed execution.
5. Review completion, pass state, counts and findings together. Correct the source, run a fresh check and test the plugin's actual behaviour.

## Why This Feels Different

Your assistant can start the official inspection and read file-level findings through the same authenticated WordPress connection. The report includes file, line, column, diagnostic code and message where Plugin Check supplies them, so a source change can begin with a specific finding.

## Before vs After

| Without the bridge | With Check Runner |
| --- | --- |
| Manually move findings from Plugin Check into an assistant conversation. | Request an inspection and read structured findings through MCP. |
| Mistake a successful scheduling request for a successful inspection. | Follow the job reference and inspect its stored final result. |
| Look only at errors. | Treat warnings as failures too. |

## Who It Is For

Plugin developers and maintenance teams who already use an authenticated MCP client and want official Plugin Check results in their development workflow.

## Requirements

- WordPress 6.9 or later, which includes the [Abilities API](https://developer.wordpress.org/apis/abilities-api/).
- PHP 8.0 or later as the compatibility minimum; use a maintained PHP release supported by your site.
- The official [Plugin Check plugin](https://wordpress.org/plugins/plugin-check/) installed and active.
- An authenticated WordPress MCP Adapter connection that exposes the required abilities. Depending on configuration, an exposure layer such as [MCP Expose Abilities](https://devenia.com/plugins/mcp-expose-abilities/) may be needed for discovery of the run action.
- WordPress cron and sufficient server resources for asynchronous work.

## Documentation

- [Check Runner product page](https://devenia.com/plugins/mcp-abilities-check-runner/)
- [WordPress Abilities API](https://developer.wordpress.org/apis/abilities-api/)
- [Official Plugin Check](https://wordpress.org/plugins/plugin-check/)

## Start Here

Ask your assistant: “Find the Check Runner actions. Run a complete inspection of the installed example-plugin in update mode. Use a background job, keep its reference, and distinguish execution failure from reported findings.”

Confirm that the account has permission for both actions before starting background work.

## Abilities

| Ability | Purpose | WordPress capability |
| --- | --- | --- |
| `plugin-check/run` | Run a synchronous inspection or schedule a background job. | `activate_plugins` |
| `plugin-check/job-status` | Read the stored state and result of one background job. | `manage_options` |

### Run an inspection

```json
{
  "plugin": "example-plugin/example-plugin.php",
  "mode": "update",
  "async": true,
  "max_results": 500
}
```

`plugin` is required. `mode` defaults to `new`; `async` defaults to `false`. `checks`, `categories` and `include_experimental` are deprecated inputs and cannot narrow the inspection.

Synchronous execution returns the inspection result directly. Background scheduling returns `completed: false`, a `job_id` and `status: pending`. Its `success` value confirms scheduling, not a pass.

### Read a background job

Call `plugin-check/job-status` with the returned reference:

```json
{"job_id": "pcr_reference_returned_by_run"}
```

The response contains `job.status`: `pending`, `running` or `complete`. Read `job.result` after completion. The outer `success` means the job record was found; it does not say the inspection passed.

A completed job can contain a clean inspection, reported findings or an execution error. Check the result fields and message rather than relying on the job state alone.

## Result Limits and Ownership

- The default response lists at most 100 findings. `max_results` is clamped to 1–500.
- Errors are listed before warnings. A large error list can hide warning details from the displayed response.
- `truncated: true` means the findings list is incomplete. Counts still cover the inspection result. There is no pagination; use the official Plugin Check interface to inspect further details.
- Background jobs depend on WordPress cron and the server's PHP execution budget. Scheduling does not guarantee that a worker starts or completes.
- Stored jobs have no cancellation or deletion ability in this version. Do not start duplicate jobs merely because one is still pending.
- Check Runner does not edit source files, install the candidate or publish a release. Plugin Check owns the inspection itself and can exercise WordPress behaviour during its checks; use a suitable test site.
- A clean inspection is one source of evidence. Functional, integration and security review remain separate work.

## Installation

1. Download the [stable ZIP](https://downloads.devenia.com/mcp-abilities-check-runner.zip).
2. Install and activate official Plugin Check on the target test site.
3. Upload the Check Runner ZIP through WordPress's plugin installation screen and activate it.
4. Configure the authenticated MCP connection and confirm that both abilities appear in discovery.

## Changelog

### 0.2.4

- Declare the official Plugin Check dependency and clarify WordPress core Abilities API requirements.
- Explain inspection scope, background jobs and response limits.

### 0.2.3

- Preserve the server-configured PHP execution budget.

### 0.2.2

- Store a terminal failure result when an asynchronous request ends unexpectedly and its shutdown handler can run.

### 0.2.1

- Use WordPress's admin-memory filter for complete inspections.

### 0.2.0

- Add background inspection jobs and a job-status ability.

### 0.1.1

- Always request the complete check set, including experimental checks; warnings fail the result.

## Contributing

Keep changes focused on the authenticated ability interface. Verify the inspection result, asynchronous state transitions and permissions affected by a change.

## License

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

## Author

[basicus](https://profiles.wordpress.org/basicus/)

## Links

- [Product page](https://devenia.com/plugins/mcp-abilities-check-runner/)
- [Stable download](https://downloads.devenia.com/mcp-abilities-check-runner.zip)
- [Optional source mirror](https://github.com/bjornfix/mcp-abilities-check-runner)
