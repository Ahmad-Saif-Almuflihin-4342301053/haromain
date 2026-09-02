# Auto-Moderator for Rejoining Team Leader - Implementation Plan

## Problem

Ketika Team Leader (TL) terputus dari Jitsi Meet dan join kembali, dia tidak otomatis menjadi moderator lagi. TL kehilangan kontrol meeting (tidak bisa mute-everyone, dll).

## Solution: JWT Token Authentication

Karena Anda punya akses ke:
- ✅ Backend API: `test.haromain.id/api`
- ✅ Jitsi Server: `jitsi.oldmapps.com`

Kita akan implement JWT token authentication untuk memberikan moderator rights secara otomatis kepada TL.

---

## Architecture Overview

```
┌─────────────┐      ┌──────────────────┐      ┌─────────────────┐
│ Flutter App │─────▶│ Backend API      │─────▶│ Jitsi Server    │
│             │      │ test.haromain.id/api  │      │ jitsi.oldmapps   │
│             │      │                  │      │                 │
│ 1. Request  │      │ 2. Generate JWT  │      │ 3. Validate JWT │
│    Token    │      │    Token with    │      │    & Grant      │
│             │      │    moderator     │      │    Moderator    │
│             │◀─────│    claim         │      │                 │
│ 4. Join     │      │                  │      │                 │
│    with     │──────────────────────────────▶│                 │
│    Token    │                                │                 │
└─────────────┘                                └─────────────────┘
```

---

## Implementation Steps

### Phase 1: Jitsi Server Configuration (jitsi.oldmapps.com)

#### Step 1.1: Configure Prosody for JWT Authentication

Edit file: `/etc/prosody/conf.avail/jitsi.oldmapps.com.cfg.lua`

```lua
VirtualHost "jitsi.oldmapps.com"
    authentication = "token"
    app_id = "haromain_meet"
    app_secret = "GENERATE_STRONG_SECRET_HERE_32_CHARS_MIN"
    allow_empty_token = false
    
    -- Optional: Enable token verification module
    modules_enabled = {
        "bosh";
        "pubsub";
        "ping";
        "speakerstats";
        "conference_duration";
        "muc_lobby_rooms";
        "muc_breakout_rooms";
        "av_moderation";
        "room_metadata";
    }
```

> [!IMPORTANT]
> **Generate Secret Key:**
> ```bash
> openssl rand -hex 32
> ```
> Simpan secret key ini, akan digunakan di backend API.

#### Step 1.2: Install Required Lua Libraries

```bash
# Install lua-cjson for JSON handling
apt-get install lua-cjson

# Install luarocks if not installed
apt-get install luarocks

# Install basexx for base64 encoding
luarocks install basexx
```

#### Step 1.3: Restart Prosody

```bash
systemctl restart prosody
systemctl status prosody  # Verify it's running
```

#### Step 1.4: Configure Jicofo (Optional but Recommended)

Edit: `/etc/jitsi/jicofo/jicofo.conf`

```hocon
jicofo {
  authentication {
    enabled = true
    type = JWT
  }
}
```

Restart Jicofo:
```bash
systemctl restart jicofo
```

---

### Phase 2: Backend API (test.haromain.id/api)

#### Step 2.1: Install JWT Library

Depending on your backend language:

**PHP (Laravel/Lumen):**
```bash
composer require firebase/php-jwt
```

**Node.js:**
```bash
npm install jsonwebtoken
```

**Python:**
```bash
pip install PyJWT
```

#### Step 2.2: Create JWT Token Generation Endpoint

**Endpoint:** `POST /meet/get-jitsi-token`

**Input:**
```json
{
  "IDPerson": "string",
  "IDVoiceMeet": "string",
  "IsLeader": boolean
}
```

**Output:**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "message": "Token generated successfully"
}
```

**PHP Implementation Example:**

```php
<?php
use Firebase\JWT\JWT;

class MeetController {
    
    private $jitsiSecret = 'YOUR_SECRET_KEY_FROM_PROSODY_CONFIG';
    private $jitsiAppId = 'haromain_meet';
    private $jitsiDomain = 'jitsi.oldmapps.com';
    
    public function getJitsiToken(Request $request) {
        $idPerson = $request->input('IDPerson');
        $idVoiceMeet = $request->input('IDVoiceMeet');
        $isLeader = $request->input('IsLeader', false);
        
        // Get user info from database
        $user = User::where('IDPerson', $idPerson)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        // Generate room name (same format as Flutter app)
        $roomName = 'meet-' . $idVoiceMeet;
        
        // Create JWT payload
        $payload = [
            'aud' => 'jitsi',
            'iss' => $this->jitsiAppId,
            'sub' => $this->jitsiDomain,
            'room' => $roomName,
            'exp' => time() + (60 * 60), // 1 hour expiration
            'nbf' => time() - 10, // Valid from 10 seconds ago
            'context' => [
                'user' => [
                    'id' => $idPerson,
                    'name' => $user->NamaJamaah ?? 'User',
                    'email' => $user->Email ?? '',
                    'avatar' => $user->FotoProfile ?? '',
                    'moderator' => $isLeader ? 'true' : 'false' // KEY: Moderator flag
                ]
            ]
        ];
        
        // Generate JWT token
        $token = JWT::encode($payload, $this->jitsiSecret, 'HS256');
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Token generated successfully'
        ]);
    }
}
```

**Route (routes/api.php):**
```php
Route::post('/meet/get-jitsi-token', [MeetController::class, 'getJitsiToken']);
```

---

### Phase 3: Flutter App Changes

#### [MODIFY] [meet_repository.dart](file:///Volumes/KyoSSD/FLUTTER_PROJECT/haromain-mobile/lib/src/data/repository/meet_repository.dart)

Add method to fetch JWT token:

```dart
// Get Jitsi JWT token for authentication
Future<Either<String, String>> getJitsiToken({
  required String idPerson,
  required String idVoiceMeet,
  required bool isLeader,
}) async {
  try {
    final response = await dio.post(
      "$serverUrl/meet/get-jitsi-token",
      options: Options(contentType: Headers.jsonContentType),
      data: {
        'IDPerson': idPerson,
        'IDVoiceMeet': idVoiceMeet,
        'IsLeader': isLeader,
      },
    );

    final json = jsonDecode(response.toString());

    if (response.ok && json['success'] == true && json['token'] != null) {
      return Right(json['token']);
    } else {
      return Left(json['message'] ?? 'Failed to get Jitsi token');
    }
  } catch (e) {
    log("Error getting Jitsi token: $e");
    return const Left("Gagal mendapatkan token autentikasi");
  }
}
```

---

#### [MODIFY] [jitsi_page.dart](file:///Volumes/KyoSSD/FLUTTER_PROJECT/haromain-mobile/lib/src/ui/jitsi/jitsi_page.dart)

Add JWT token parameter and use it in conference options:

```dart
class JitsiMeetingPage extends StatefulWidget {
  final String? serverUrl;
  final String? roomName;
  final String? userName;
  final bool isLeader;
  final String? idVoiceMeet;
  final String? idPerson;
  final DateTime? meetEndTime;
  final String? jwtToken; // NEW: JWT token parameter

  const JitsiMeetingPage({
    super.key,
    this.serverUrl,
    this.roomName,
    this.userName,
    this.isLeader = false,
    this.idVoiceMeet,
    this.idPerson,
    this.meetEndTime,
    this.jwtToken, // NEW
  });
}

// In _joinMeeting() method, update JitsiMeetConferenceOptions:
var options = JitsiMeetConferenceOptions(
  serverURL: "https://jitsi.oldmapps.com",
  room: roomName,
  token: widget.jwtToken, // NEW: Add JWT token
  configOverrides: {
    // ... existing config
  },
  // ... rest of options
);
```

---

#### [MODIFY] [meet_item.dart](file:///Volumes/KyoSSD/FLUTTER_PROJECT/haromain-mobile/lib/src/ui/meet/meet_item.dart)

Fetch JWT token before joining meeting:

```dart
void _joinMeet() async {
  // ... existing session active check code ...

  // NEW: Fetch JWT token before joining
  String? jwtToken;
  final repository = MeetRepository();
  final tokenResult = await repository.getJitsiToken(
    idPerson: widget.personId,
    idVoiceMeet: widget.meet.idMeet ?? '',
    isLeader: widget.isLeader,
  );

  tokenResult.fold(
    (error) {
      debugPrint('Failed to get JWT token: $error');
      // Show error but still allow joining (fallback to no token)
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Peringatan: $error'),
            backgroundColor: Colors.orange,
            duration: const Duration(seconds: 2),
          ),
        );
      }
    },
    (token) {
      jwtToken = token;
      debugPrint('JWT token obtained successfully');
    },
  );

  // Call join API
  final success = await ref.read(meetNotifierProvider.notifier).joinMeet(
        idVoiceMeet: widget.meet.idMeet ?? '',
        idPerson: widget.personId,
      );

  if (!mounted) return;

  if (success) {
    final roomName =
        'meet-${widget.meet.idMeet ?? DateTime.now().millisecondsSinceEpoch}';
    final person = ref.read(SessionNotifier.provider).valueOrNull;
    final userName = person?.namaJamaah ?? 'User';

    // Navigate to JitsiPage with JWT token
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => JitsiMeetingPage(
          roomName: roomName,
          userName: userName,
          idPerson: widget.personId,
          idVoiceMeet: widget.meet.idMeet,
          meetEndTime: widget.meet.endDateTime,
          isLeader: widget.isLeader,
          jwtToken: jwtToken, // NEW: Pass JWT token
        ),
      ),
    );
  } else {
    // ... existing error handling ...
  }
}
```

---

## Verification Plan

### Manual Testing Checklist

- [ ] **Server Configuration**
  - [ ] Prosody configured with JWT authentication
  - [ ] Secret key generated and configured
  - [ ] Prosody restarted successfully
  
- [ ] **Backend API**
  - [ ] JWT library installed
  - [ ] Endpoint `/meet/get-jitsi-token` created
  - [ ] Test endpoint with Postman/curl
  - [ ] Verify token payload structure
  
- [ ] **Flutter App**
  - [ ] Code changes applied
  - [ ] App builds successfully
  
- [ ] **Integration Testing**
  1. TL creates meeting → TL joins → Verify TL is moderator (has mute-everyone button)
  2. Jamaah joins → Verify Jamaah is NOT moderator
  3. TL leaves meeting (disconnect)
  4. TL rejoins → **Verify TL is moderator again** ✅
  5. Verify moderator controls work (mute-everyone)
  
- [ ] **Edge Cases**
  - [ ] Token fetch fails → Verify graceful fallback
  - [ ] Invalid token → Verify error handling
  - [ ] Token expired during meeting → Test behavior
  - [ ] Multiple TLs in same meeting

---

## Troubleshooting

### Issue: "Authentication failed" when joining

**Solution:** Check Prosody logs:
```bash
tail -f /var/log/prosody/prosody.log
```

Verify `app_id` and `app_secret` match between Prosody config and backend API.

### Issue: Token validation fails

**Solution:** Verify JWT payload structure matches Jitsi requirements. Use [jwt.io](https://jwt.io) to decode and inspect token.

### Issue: Moderator rights not granted

**Solution:** Check that `context.user.moderator` is set to string `"true"`, not boolean `true`.

---

## Security Considerations

> [!WARNING]
> **Secret Key Security**
> - Never commit secret key to version control
> - Use environment variables for secret key
> - Rotate secret key periodically
> - Use different secrets for dev/staging/production

> [!TIP]
> **Token Expiration**
> - Set reasonable expiration (15-60 minutes)
> - Implement token refresh if meetings are longer
> - Handle token expiration gracefully in app
