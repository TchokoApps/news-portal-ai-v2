---
name: laravel-module-quick-checklist
description: Fast 5-10 step checklist for implementing CRUD modules in Laravel News Portal AI. Use when you need rapid module creation without detailed explanations.
applyTo: ["**/*.php", "**/*.blade.php", "routes/admin.php"]
---

# Laravel Module Quick Checklist

Quick reference for implementing modules in News Portal AI. Use this when you know what you're doing and need speed.

## ⚡ 10-Step Module Implementation

### Step 1: Create Model & Migration
```bash
php artisan make:model ModelName -m
```
- Add `$fillable` array
- Define relationships (belongsTo, hasMany, etc.)
- In migration: add columns, foreign keys with `cascadeOnDelete()`

### Step 2: Create Controller & Request
```bash
php artisan make:controller Admin/ModelNameController --resource
php artisan make:request Admin/ModelNameStoreRequest
php artisan make:request Admin/ModelNameUpdateRequest
```

### Step 3: Add Validation Rules
In Form Request:
- `required`, `string`, `max:255` for text
- `required|exists:table_name,id` for foreign keys
- `nullable|image|max:2048` for uploads

### Step 4: Implement CRUD Logic
Controller methods:
- `index()` - List with eager loading: `Model::with('relationships')->get()`
- `create()` - Show form
- `store()` - Validate, save, redirect with message
- `edit()` - Show form with data
- `update()` - Validate, update, redirect
- `destroy()` - Delete with SweetAlert confirmation

### Step 5: Create Views
Location: `resources/views/admin/modulename/`

Files needed:
- `index.blade.php` - DataTable listing
- `form.blade.php` - Create/Edit form (reusable)
- `create.blade.php` - `@include('form')`
- `edit.blade.php` - `@include('form')`

Template: `@extends('admin.layouts.master')`

### Step 6: Register Routes
In `routes/admin.php`:
```php
Route::resource('module-name', Admin\ModelNameController::class)
    ->middleware('auth:admin')
    ->names('admin.module-name');
```

### Step 7: Add Delete Confirmation
In index view:
```blade
<form method="POST" action="{{ route('admin.module-name.destroy', $item->id) }}" style="display:inline;">
    @csrf
    @method('DELETE')
    <button onclick="return confirm('Sure?')" class="btn btn-danger btn-sm">Delete</button>
</form>
```

Or use SweetAlert:
```javascript
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        if(confirm('Sure?')) this.closest('form').submit();
    });
});
```

### Step 8: Optimize Queries
Controller:
```php
Model::with('relationship1', 'relationship2')
    ->withCount('count_field')
    ->paginate(15);
```

### Step 9: Add Error Handling
```php
try {
    // operation
    return redirect()->back()->with('success', 'Created');
} catch(Exception $e) {
    return redirect()->back()->with('error', $e->getMessage());
}
```

### Step 10: Clear Cache & Test
```bash
php artisan optimize:clear
php artisan migrate
```
Then test in browser: `http://localhost/admin/module-name`

---

## 🎯 Common Pitfalls to Avoid

❌ **Forget eager loading** → N+1 queries  
✅ Use `::with(['relationships'])`

❌ **Missing validation** → Security risk  
✅ Always use Form Requests

❌ **No error handling** → White screens  
✅ Wrap DB operations in try-catch

❌ **Hardcoded route names** → Maintenance nightmare  
✅ Use `route('admin.module.action')`

❌ **No delete confirmation** → Accidental deletions  
✅ Add SweetAlert or confirm() dialog

---

## 📋 File Checklist Before Going Live

- [ ] Model has `$fillable` array
- [ ] Relationships defined with type hints
- [ ] Migration has timestamps, foreign keys, cascades
- [ ] Controller uses try-catch for DB operations
- [ ] Form Requests validate all inputs
- [ ] Views extend `admin.layouts.master`
- [ ] Routes use named routes (`admin.module.action`)
- [ ] Delete has confirmation dialog
- [ ] Queries use eager loading (`.with()`)
- [ ] Cache cleared with `php artisan optimize:clear`

---

## 🚀 Command Reference

```bash
# Create full module scaffold
php artisan make:model ModuleName -mfsr

# Generate individual pieces
php artisan make:model ModuleName -m          # Model + Migration
php artisan make:controller Admin/ModuleController --resource
php artisan make:request Admin/ModuleStoreRequest
php artisan make:request Admin/ModuleUpdateRequest

# Database
php artisan migrate
php artisan migrate:rollback

# Cache
php artisan optimize:clear
php artisan route:cache
```

---

## 💡 Pro Tips

1. **Use existing blade components** - Check `resources/views/components/`
2. **Follow naming convention** - `admin.module-name.action` for routes
3. **Reuse form partial** - Create form, include in create & edit views
4. **Use DataTables for large lists** - Already installed in Stisla
5. **Cache frequently accessed data** - Use `Cache::remember()`

---

## 🎨 Quick View Template

```blade
@extends('admin.layouts.master')

@section('content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Module List</h4>
                    <a href="{{ route('admin.module-name.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="table-1">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->status ? 'success' : 'danger' }}">
                                        {{ $item->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.module-name.edit', $item->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.module-name.destroy', $item->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">No records found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#table-1').dataTable();
    });
</script>
@endpush
```

---

## ✅ When to Use This Skill

✅ You know the patterns and want a quick reference  
✅ You're implementing a standard CRUD module  
✅ You need a checklist to verify completion  
✅ You want to avoid common mistakes

❌ First time building a module → Use detailed guide instead  
❌ Complex business logic → Use detailed guide + service layer  
❌ Need explanations → Use detailed guide
