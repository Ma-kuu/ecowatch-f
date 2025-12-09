# Vue.js Components Usage Guide

## 🎨 Components Available

### 1. Image Upload Preview
### 2. Toast Notifications

---

## 📸 **Image Upload Preview Component**

### **Usage in Blade Templates:**

```blade
<div id="app">
  <image-upload-preview 
    name="photo" 
    accept="image/jpeg,image/png,image/jpg,image/heic"
    :max-size-m-b="10"
    :required="true"
  ></image-upload-preview>
</div>

@vite(['resources/js/app.js'])
```

### **Props:**
- `name` - Input field name (default: 'photo')
- `accept` - Accepted file types (default: 'image/jpeg,image/png,image/jpg,image/heic')
- `max-size-m-b` - Max file size in MB (default: 10)
- `required` - Is field required (default: false)

### **Features:**
✅ Live image preview
✅ File size validation
✅ File type validation
✅ Remove/change image
✅ Displays file name and size
✅ Drag-and-drop ready

---

## 🔔 **Toast Notification Component**

### **Usage in Blade Templates:**

```blade
<div id="app">
  <toast-notification></toast-notification>
  <!-- Your content -->
</div>

@vite(['resources/js/app.js'])
```

### **Trigger Toasts from JavaScript:**

```javascript
// Success toast
showToast('Report submitted successfully!', 'success');

// Error toast
showToast('Something went wrong!', 'danger');

// Warning toast
showToast('Please fill all fields', 'warning');

// Info toast
showToast('Processing your request...', 'info');

// Custom duration (5 seconds)
showToast('This will stay for 5 seconds', 'success', 5000);
```

### **Trigger from Laravel Controller:**

```php
return redirect()->back()->with('toast', [
    'message' => 'Profile updated successfully!',
    'type' => 'success'
]);
```

Then in your Blade layout:

```blade
@if(session('toast'))
<script>
  document.addEventListener('DOMContentLoaded', function() {
    showToast('{{ session('toast.message') }}', '{{ session('toast.type') }}');
  });
</script>
@endif
```

---

## 🚀 **Quick Integration Examples**

### **Example 1: Report Form with Image Preview**

```blade
@extends('layouts.app')

@section('content')
<div id="app">
  <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- Other form fields -->
    
    <div class="mb-3">
      <label class="form-label">Upload Photo</label>
      <image-upload-preview 
        name="photo" 
        :required="true"
      ></image-upload-preview>
    </div>
    
    <button type="submit" class="btn btn-primary">Submit Report</button>
  </form>
  
  <toast-notification></toast-notification>
</div>
@endsection

@push('scripts')
@vite(['resources/js/app.js'])
@endpush
```

### **Example 2: Success Message After Form Submit**

```php
// In Controller
public function store(Request $request)
{
    // ... save logic
    
    return redirect()->route('dashboard')->with('toast', [
        'message' => 'Report created successfully!',
        'type' => 'success'
    ]);
}
```

```blade
<!-- In Layout -->
@if(session('toast'))
<script>
  showToast('{{ session('toast.message') }}', '{{ session('toast.type') }}');
</script>
@endif
```

---

## 📦 **Build Assets**

After making changes, rebuild assets:

```bash
npm run dev    # For development
npm run build  # For production
```

---

## ✅ **Benefits**

### Image Upload Preview:
- ✅ Users see their image before submitting
- ✅ Prevents wrong file uploads
- ✅ Better UX with visual feedback
- ✅ File validation before server upload

### Toast Notifications:
- ✅ Non-intrusive notifications
- ✅ Auto-dismiss after timeout
- ✅ Multiple toasts support
- ✅ Beautiful animations
- ✅ Consistent across the app

---

## 🔧 **Customization**

### Change Toast Duration:
```javascript
showToast('Message', 'success', 10000); // 10 seconds
```

### Change Max File Size:
```blade
<image-upload-preview :max-size-m-b="5"></image-upload-preview>
```

### Custom File Types:
```blade
<image-upload-preview accept="image/png,image/jpg"></image-upload-preview>
```

---

## 🎯 **Next Steps**

1. Run `npm run dev` to compile assets
2. Add `@vite(['resources/js/app.js'])` to your layouts
3. Wrap content in `<div id="app">` where you use components
4. Use the components in your Blade templates!

Enjoy your new Vue.js components! 🎉
