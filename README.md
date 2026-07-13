# MCP Abilities - Check Runner

MCP bridge for the official WordPress.org Plugin Check plugin.

[![GitHub release](https://img.shields.io/github/v/release/bjornfix/mcp-abilities-check-runner)](https://github.com/bjornfix/mcp-abilities-check-runner/releases)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![WordPress](https://img.shields.io/badge/WordPress-6.9%2B-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)](https://php.net)

**Tested up to:** 7.0
**Stable tag:** 0.1.4
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

## What It Does

MCP bridge for the official WordPress.org Plugin Check plugin.

This plugin is part of the MCP abilities ecosystem. It gives an MCP-capable agent a focused, authenticated way to work with Plugin Check work inside WordPress through MCP.

**Example:** "Handle this WordPress maintenance task directly." - The agent can inspect the site, call the relevant ability, and return the result without making the human click through wp-admin for every step.

## The Real Workflow

In practice, the human should not have to memorize every ability name.

The normal pattern is:

1. install the base MCP stack
2. install only the add-ons the site actually needs
3. let the agent discover the available abilities
4. give the agent a clear task with boundaries
5. verify the result in WordPress

The human's job is mostly to describe the goal.
The agent's job is to figure out the mechanics.

## Why This Feels Different

Most WordPress automation still leaves the repetitive part to the human.

This plugin is different because the agent can act inside the site through a narrow, authenticated ability surface:

- inspect current site state before changing anything
- run the specific action needed for the task
- return structured results that are easy to verify
- keep the workflow inside WordPress instead of a separate checklist

That changes the experience from:

- `Here is what you should do in wp-admin`

to:

- `Tell the agent what needs doing, and let it carry out the work`

## Before vs After

### Before

- ask the AI what to do
- copy the answer into WordPress by hand
- click through wp-admin for the repetitive bits
- postpone maintenance because the task is tedious

### After

- tell the agent what needs doing
- let it inspect the relevant WordPress state
- let it run the targeted ability
- verify the result and move on

## Who It Is For

This is a good fit for:

- agencies managing WordPress sites with AI-assisted maintenance
- operators who want agents to do real WordPress work instead of producing instructions
- teams already using MCP Expose Abilities
- sites where this WordPress area is updated often enough to deserve automation

It is especially useful when the manual version is repetitive enough that important maintenance gets delayed.

## Documentation

Start with the base stack documentation:

- [MCP Expose Abilities](https://github.com/bjornfix/mcp-expose-abilities)
- [Getting Started](https://github.com/bjornfix/mcp-expose-abilities/wiki/Getting-Started)
- [Install Order and Dependencies](https://github.com/bjornfix/mcp-expose-abilities/wiki/Install-Order-and-Dependencies)

If you are using an AI agent, the simplest instruction is often just:

- `Read https://github.com/bjornfix/mcp-expose-abilities and figure out the stack before making changes.`

## Start Here

If you are new to the stack, use this order:

1. Install **Abilities API**.
2. Install **MCP Adapter**.
3. Install **MCP Expose Abilities**.
4. Install **MCP Abilities - Check Runner**.
5. Confirm the new abilities appear in discovery.
6. Give the agent a clear task that uses this add-on.

If you skip base-stack verification and start with add-ons immediately, troubleshooting gets harder than it needs to be.

## Abilities

- `plugin-check/run` - always runs the complete check set, including experimental checks. Warnings fail the gate.

## Notes

The official Plugin Check plugin must be installed and active on the target WordPress site.

## Changelog

### 0.1.4

- Raises the bounded analysis ceiling for large plugins to 1 GB and five minutes, then restores request limits.

### 0.1.3

- Gives the bounded Plugin Check/PHPCS run a 512 MB analysis allowance and restores the request memory limit afterward.

### 0.1.2

- Scopes Devenia source-design gate bypass to Plugin Check's own disposable publication fixtures so the official runner can complete without weakening normal publication requests.

### 0.1.1

- Changed `plugin-check/run` to always run the complete Plugin Check set.
- Deprecated caller-side check/category filtering so release gates cannot accidentally skip checks.
- Treat warnings as failed gate results; success is only true with zero errors and zero warnings.

### 0.1.0

- Documentation aligned with the public plugin README standard.

## Contributing

PRs welcome. Keep changes focused on the plugin's WordPress ability surface and preserve authenticated, explicit workflows.

## License

GPL-2.0+

## Author

[basicus](https://profiles.wordpress.org/basicus/)

## Links

- [MCP Expose Abilities](https://github.com/bjornfix/mcp-expose-abilities)
- [GitHub Releases](https://github.com/bjornfix/mcp-abilities-check-runner/releases)

## Star and Share

If this plugin saves you time or makes WordPress maintenance easier to verify, please:

- star the repo
- share it with people running WordPress sites
- point them to the main plugin page so they can see what the ecosystem can actually do

Why do it?

Because agent-friendly open WordPress tooling helps more of the boring but important work get done.
