# `lyre/commerce` Agent Guide

## Package Purpose
`lyre/commerce` provides e-commerce workflows (catalog, variants, pricing, couponing, orders, cart, checkout) on top of Lyre conventions.

## What Belongs In This Package
- Commerce domain models/repositories/controllers/resources.
- Cart/checkout service layer.
- Commerce commands and config (`src/config/commerce.php`).
- Commerce Filament resources/plugin.

## What Does Not Belong Here
- Generic CMS page composition (`lyre/content`).
- Generic guest identity internals (`lyre/guest`), though this package may depend on them.

## Public API / Stable Contracts
- Config key `commerce.route_prefix` and route surfaces in `src/routes/api.php`.
- Resource endpoints plus `cart/*` and `checkout/*` domain endpoints.
- Service expectations used by controllers (`CartService`, `CheckoutService`, etc.).

## Internal Areas That May Change
- Internal pricing/calculation implementation details preserving endpoint outputs and persisted state transitions.

## Usage Rules
- Use this package for order/cart/checkout concerns; keep catalog and order lifecycle logic here.
- Ensure `lyre/guest` middleware compatibility because commerce routes include `EnsureGuestUser`.

## Extension Rules
- Add new commerce domain entities in this package, not in core.
- Preserve existing route names and expected payloads unless versioned.
- Keep config-driven behavior backward compatible (new keys should be additive with safe defaults).

## Testing Requirements
- Validate cart add/remove/summary and coupon application flows.
- Validate checkout confirmation/payment flow and order state changes.
- Validate API behavior under configured route prefix.

## Docs To Update When This Package Changes
- Root [AGENTS.md](/Users/chegekigathi/Projects/packages/lyre-packages/AGENTS.md)
- [docs/package-responsibilities.md](/Users/chegekigathi/Projects/packages/lyre-packages/docs/package-responsibilities.md)
- `packages/commerce/README.md` and `packages/commerce/docs/README.md`
