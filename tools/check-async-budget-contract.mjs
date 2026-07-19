import fs from 'node:fs';
import path from 'node:path';

const root = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..');
const php = fs.readFileSync(path.join(root, 'mcp-abilities-check-runner.php'), 'utf8');

for (const [pattern, description] of [
  [/const MCP_CHECK_RUNNER_ASYNC_TIME_LIMIT_SECONDS = 600;/, 'fixed ten-minute server ceiling'],
  [/set_time_limit\( MCP_CHECK_RUNNER_ASYNC_TIME_LIMIT_SECONDS \)/, 'async worker applies the server ceiling'],
  [/'async_job_terminated'/, 'terminal shutdown receipt'],
  [/max\( 1, min\( 500, \(int\) \$input\['max_results'\]/, 'result details remain bounded'],
]) {
  if (!pattern.test(php)) throw new Error(`Contract assertion failed: ${description}`);
}

for (const [pattern, description] of [
  [/\$input\[['"](?:timeout|time_limit|execution_time)['"]\]/, 'caller-controlled execution ceiling'],
  [/set_time_limit\( 0 \)/, 'unbounded execution time'],
]) {
  if (pattern.test(php)) throw new Error(`Forbidden pattern found: ${description}`);
}

console.log(JSON.stringify({ success: true, assertions: 4, forbidden_checks: 2 }));
