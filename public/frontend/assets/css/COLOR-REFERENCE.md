# Uzimate Color System - Quick Reference

This document provides a quick reference for using Uzimate brand colors throughout your project.

## 🎨 CSS Variables

All colors are defined as CSS variables in `brand-colors.css`:

```css
--uzimate-purple         #4A148D  (Primary brand color)
--uzimate-purple-dark    #4A246F  (Dark variant)
--uzimate-purple-light   #F8F5FC  (Light background)
--uzimate-yellow         #FFD700  (Accent color)
--uzimate-yellow-bright  #FFC107  (Bright variant)
--uzimate-text-dark      #333333  (Dark text)
--uzimate-text-light     #666666  (Light text)
--uzimate-text-white     #FFFFFF  (White text)
```

## 📝 Usage Examples

### 1. Using CSS Variables Directly

```css
.my-element {
  background-color: var(--uzimate-purple);
  color: var(--uzimate-text-white);
}
```

### 2. Using Utility Classes

```html
<!-- Background Colors -->
<div class="bg-uzimate-purple">Purple Background</div>
<div class="bg-uzimate-yellow">Yellow Background</div>
<div class="bg-uzimate-purple-light">Light Purple Background</div>

<!-- Text Colors -->
<p class="text-uzimate-purple">Purple Text</p>
<p class="text-uzimate-yellow">Yellow Text</p>
<p class="text-uzimate-dark">Dark Text</p>

<!-- Borders -->
<div class="border border-uzimate-purple">Purple Border</div>
```

### 3. Using Button Classes

```html
<!-- Purple Button -->
<button class="btn btn-uzimate-purple">Click Me</button>

<!-- Yellow Button -->
<button class="btn btn-uzimate-yellow">Click Me</button>

<!-- Outline Button -->
<button class="btn btn-uzimate-outline">Click Me</button>
```

### 4. In Laravel Blade Templates

```blade
<!-- Using utility classes -->
<div class="bg-uzimate-purple text-white p-4">
    <h1 class="text-uzimate-yellow">Welcome to Uzimate</h1>
</div>

<!-- Using inline styles with variables -->
<div style="background-color: var(--uzimate-purple); color: var(--uzimate-text-white);">
    Content here
</div>
```

### 5. In Custom CSS (Laravel)

```css
/* In your Laravel app.css or custom stylesheet */
.my-custom-component {
    background: var(--uzimate-purple);
    border: 2px solid var(--uzimate-yellow);
    color: var(--uzimate-text-white);
}

.my-custom-component:hover {
    background: var(--uzimate-purple-hover);
}
```

## 🎯 Common Use Cases

### Headers/Navigation
```html
<nav class="bg-uzimate-purple">
    <a href="#" class="text-white">Link</a>
</nav>
```

### Hero Sections
```html
<section class="bg-uzimate-purple-light">
    <h1 class="text-uzimate-purple">
        <span class="text-uzimate-yellow">Highlight</span> Text
    </h1>
</section>
```

### Feature Cards
```html
<div class="bg-uzimate-purple text-white">
    <i class="text-uzimate-yellow">⭐</i>
    <h3>Feature Title</h3>
</div>
```

### Call-to-Action Buttons
```html
<a href="#" class="btn btn-uzimate-yellow">Start for Free</a>
<a href="#" class="btn btn-uzimate-purple">Learn More</a>
```

## 🔄 Color Combinations

### Primary Combinations
- Purple background + Yellow text/accents
- Yellow background + Purple text
- White background + Purple text + Yellow accents
- Purple background + White text + Yellow highlights

### Example Gradient
```css
.gradient-uzimate {
    background: linear-gradient(135deg, var(--uzimate-purple) 0%, var(--uzimate-yellow) 100%);
}
```

## 📱 Responsive Considerations

All color classes work seamlessly across all screen sizes. The color system is designed to maintain brand consistency regardless of device.

## 🛠️ Customization

To change brand colors, edit the CSS variables in `brand-colors.css`:

```css
:root {
  --uzimate-purple: #YOUR_NEW_COLOR;
  --uzimate-yellow: #YOUR_NEW_COLOR;
}
```

All components will automatically update to use the new colors!

---

**Pro Tip**: Always use CSS variables or utility classes instead of hardcoding color values. This ensures consistency and makes future updates easier.



