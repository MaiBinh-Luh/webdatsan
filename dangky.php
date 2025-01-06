<?php
require_once('./initialize.php');

// Kết nối tới cơ sở dữ liệu
try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Thiết lập chế độ lỗi
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());  // In ra lỗi nếu không thể kết nối
}

// Biến message để truyền vào JavaScript
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $type = 2; // Mặc định là người dùng bình thường

    // Kiểm tra dữ liệu
    if (empty($firstname) || empty($lastname) || empty($username) || empty($password)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Kiểm tra username đã tồn tại
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Tên đăng nhập đã tồn tại!";
        } else {
            // Mã hóa mật khẩu
            $hashed_password = md5($password); // Không an toàn, bạn nên dùng `password_hash`
            $sql = "INSERT INTO users (firstname, lastname, username, password, type, date_added) 
                    VALUES (:firstname, :lastname, :username, :password, :type, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $username,
                'password' => $hashed_password,
                'type' => $type
            ]);
            
            // Đặt thông báo thành công
            $message = "success";  // Đặt thông báo khi đăng ký thành công
            // Điều hướng tới trang tài khoản hoặc trang khác nếu cần
            header("Location: " . base_url . "/");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-image: url("data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxMTEhUTExIWFRUXFxgYGBgYGBoWHRUYGhgaGBoZGhcaHSggGB0lHxgYITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGhAQGzUlHyUtLS0tLS0tLS0tLS0tLS8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS4tLS0tLf/AABEIALcBEwMBIgACEQEDEQH/xAAbAAABBQEBAAAAAAAAAAAAAAADAAECBAUGB//EAEcQAAEDAgMDCAYHBgUDBQAAAAEAAhEDIQQSMQVBUQYTImFxgZGhMlKSsdHSI0JicoLB8BQVM1Oi4RZDc5PxRLLCB1Rjg+L/xAAZAQADAQEBAAAAAAAAAAAAAAAAAQIDBAX/xAArEQACAgEDBAEDBAMBAAAAAAAAAQIRIQMSMQQTQVGhFCJhcZHh8DJCgSP/2gAMAwEAAhEDEQA/APO6bKc7vIrRwtamQQ4gAg6tkgQPR6+rt10WJT1Vig7MeswOw8LrGUbRiW8cMjyxzQHDh1jj4JsLjgG+g09ZAtwVOvMkkyeJ8VKlh/o3PNRgI+rJzT9W2WNxOu68ISwFHQ4TaFAgS1oO+wN+paGF2nQpvBc1pbebA6iBaOMLhK74Nrd6LhnEzfUR4pzW6Di/I1aydNtigHA1Gu6LyB0WCIkkSdxvcdYkyq+wKNN1cAlu8QRrFt40160B+2wyGsaGsBGcAZg4dEEkEwbC2/rXQ8ndjufWc91INaC40w4ZTUsIMGI3brSuGUnpwe7/AIaRW6WDTqYJgyBzW9B4hhEkNBJ6XrSANDu3lZ22MKHuNQ02tY0XAAEEzrAjeFc/a3U6z2VHNguhokyJi2kAiHHjfUqpyh2g2oOao9OoXWaOiHZtCDpaD3LHSlPcjWdNGDinMnoMGpsIv+u1U3YwAwWjXcBqiUcK85mgAvDgzj0iYsd4ssavUJdJ/wCV6aycrRs0MdlOjb5pEDgYjgfigVcX0Q0NFnEgxe+oJ81RoOBcN/6Av4rqtp7DYXk0nNGSgwgC+d5hu/TtvxWUtsJKykm0c/SrkS4gX0sOtWWYyG9Jg8LzHGFQxwNN5YTIaYtpPV7lXfWJIFybADjaLQtdt5Io3KeMaRJY39f3RG46nYc22exQ2dsKvVE5ebZMFz5EEDgbnXyV+jyepc7k/aHDok5ixrRAPEvJmJ3bisLhnPA9jBU8e2bsYAATdvVYd5VzCbRouABa0Ej1R741Vbb+wWMksqEnO1oBgWvJMb7s8+KzcXs59MB2ZrhYHLMg3Gm/TVT24akbHmLOm56jwae4W8E5xFIXyg9QaLLlKdf6N/S1ibi2uu9Qw+0iNROuql9L+Q3s65mKa5ri2jmIE3ZA4T5+apM2tSd0XNAHU0XXPnajmh7Wus8XjsjTdYkd6pCtoiPS+x7mejbDfhnuDCzKTc2aZDSSLbrRa+iy9q42nSfUpcy003ukC17RAPlxESsjk5tFrHkkDogumdPNQ27tcODmWkOs4QZBMkTwkTP3R1rN6L7teK/rNN32g8VtgvaaeVhgxOW7gDNzuMAXEaKjTp2JgANyzpvNu/VVqzgXEgQLQO4T4mVKYmTJtby7rLujBRWDNuy41jSTpPYrOB2e+o7K2mMti5xAADSYmVTqNaKYcHNJ4efef+F0OE2gDhhDw1xDmwSJO/dplJkTx61nqzaWAjG3kzKmCaxxa/KIMaTPGFvcnNg06pdVcQ1jCWgASXOLTEnQATO9cuxzKjXOc9+fMMrWsDsxNwJzAgyHE2Nh3La5MYkvqBrKZLYAqZZMjMJmZg693Yo6juLTdOmVBfdk7KniGMAp8010EBrg3NHRBnSXTrFlgcoMaym40mUgXucLlsQBOhIHE6HitLAVHPqPrktZDXMYC6SQHOZm06MQO3vWLymwDs/Og54OR0dG1g0NEng7Urh6TUenq59fJrO6wWg82gU4gerwSWK/B1pP0ZPWN6S6f/R/7mdv0Yf7CTq8DvU24A7nNIlGrsboB4anw1V/BbKDSHVrN3MF3HrPBeipprgxTszWbGrPnLoJJPCL3PchUMBcg1BcHSTcX4dUd66HHc4WuIcGt0yzBjs/MqlhdkPtUJDWghwLpvEGAPzWXeiv0Kv0S2HsOiWVKlR0wBAIiZgg/wB+1ZGKp0sx6RbewAkR4rrqGzaIcScQ0TAAg2aRIJ4iDASqYTDh0g5mN0BI6RG8rnXUVN+S2sGFyTDP2ukXOLrkAEaktOt12O0nOFSc8Ogvg36IFwL27epZ2HwmGs94E3hrJPeXHuVrE4ukZIsS0tuAS0bog8Z3rj6iXc1FJLxRcXSyc7tnHHEVA6CGk5nTcNkC7stgJA1/NZrMcWVgXEy21rWFhv3Bbr6FMUyC58OAJY12UOI7Zj9dqpN2RTPSN2zYTvmNYMwd+9denOMVXgzfJHZm0msxPObjJIPEjL7lhbQaw1HESATMWt1BdVQ2UxlSDzbnXHS6Xlu81Kvg2NgAU5N5IJgdTSI139SqOtGMrXoMnJ4VrWua6Tff+uxaTsaWuBDjcgCTuGg8571sP2ZSBDXOBmDDYgzcROhutGpgaAYyjLozZxOU3gg3i2iJ9TF1gVNnLU9ntr4mlSJPTdBIjTUkdcLpHYPD4Os3KIcKbmySTc2BA3GPC6sYTD4enUM5cxIykCTYzIO4yB5pq5pPfFRrcwMATPcZOu+fJc2prSm68UaRqK/JexT89CJ+rmdu0IOmpMuJ7lxWJxLmZHSbybxBbndE8dF2ALQ0Oa1rA0jol2YOAJOW3oi+hVJ2Fpuc2u5jHDpkMNh/Edv6jHbdR08tm6+AnUjnsfjzUByg/UiNBqXSOs3H3VawxLwHE6McI3S2wPhfhY6rRrABjmhlFrHPzRAJBAtBjUZvNaeGwVPI1tVzYhpLWNjM3t0FrSIN1rLWUYrFEqKbOCeKRJcQ694B4lOx1H+W72iuy5SYXDCpmpNDQ76s6QALNiA3vVGlgWlw6Ig9cTp3710w6pSinRDTTow8PzJP8OIEyST5I+KoUmsFTm7Ex6REkgmw4WWy2lSB6TWOBzDUi9xPXGtuEK3VwFBmXM1jjAiJ0N7N3m58FMupSkUo3k5MupX+iI49I/BbnJLk5RxbqheHsYwC4dJLjo0SI0BPgtHE4bDmoGs3iB0RqNTpA4rW2FtmnSa2kbiSbDWdNIEWKx1+rn2321kuEVuycryr2ZhqVQU6NNxImYcXaEzu3SFzoFPXmz7S7TFPc6q+ox7Q2SRlHSgtMgE9QggcVDDU20y0OqCHEHK2Y6u2x1V6XUuMEpZZMlk5IZN1M9V0RtGnmAfScJiTO6ddF2Z5onMDdom8tDrQRbpCLhVG4MPOYvblzAFoLj0joJ1AKr6r8C2tG1sfCYemW/stAue5rjmzTDRIJJcYBJDmzbgEn7RNIvzjKSQ4E2zaXJHu4HqhEoYmnRccsaZZvOVoAmSZIhrTYRdZu062Hr5C8kFoLoAuSY6JuDoB4rzXF6kmpW17N26RgY3aB5wlpbOpgwO/xQRtSo94aJcToB0ulc6bzrftVnEGk4ktYAAbSCbGYm8lE2RSe2pmotbngiYFgYk69lyu7aox4/c5nLJjYnbFUOIc5wIMETERuhJWXh7SRlbqde2UluttcBu/JrUKNOjGUS46vIv+EKbsQA49Fp0NwS4kdl/NUaYLyYJjeTx/MqyKJY3MZji5abHPngUVKRVx+NDCCWgDeDcXtMDTRC2jtV9Xo526RvFo4dyp7SrAwHReZmYGYETa/RIDu5U6LzTaHAnM6cp3hoME67yI/CVS6eGGa4jybOG2TiMQxjqLRUIGV2WXEQbH0bWcB3IzOTG0HDLzDrEmS14nvLVW2Ryor0CS17nZhHSJgdnS1VvBcssUxwJr1HRNnQQZEX48Vr2o+SHKPoQ5M4+Y5h2hHovPHflO9PW5P41jjmomwByjMSA6Y0bocp8FPE8ssS8yKlRnpGGGBdxdvnSYQ8VytxFRjWOq1LR0gSHGC4iXA/ajsA65T0oApL0WKXJrHOb0aR0gmHaWFgWg8LdSt7O5MY4NA5h1ifSBbq4EXIHCfyVXAcqK9OnUYKlU84IJc4uLY3sM9E3KNg+VOIZP01Z+npGYhzXafhjsJWUtPT4NFKJMckMeHEikTaJgkEXtPZA7lVxfJTGBzc1MyMsiCY6pHYj4nlPXqPL+erNmOix2VtgBYA20T7S5T1qwY1z6oDQACxzmkwIlzgZJRsh/UG6JUxnJ/E860EZc5im0yZItA00kcNQjHk1i7uhwGUzIIgRfXWw0v5odPbVQFnTruyPL25nOcZOWxJNx0BbrPFDo7VqtcCKuJMbnPe4eBN0u3H+odwKrcJVkkPkj7LjPHt/5UWYGsDOYnf6Lzu/uUejWc2MvOm89KXjT1XSD3ojHVftn/wCtvyJKOmv8n8EuS8L5BVKdYgg8dAx+uoMfiKsVsFUFOi0S4BrpORxE53jun8kN+GqkzNYdTeiPANTPw9efSqwLAQ615vxuTqsvt4XBdr0V6+DrOAnPYnSm68nWFfFLEEiGuAaLA0nxpln9cVWNCpr9IfwM+RFbTfvbWIiCB0fc26uS0aw/j+SYyzlfJVdsiu85vpDfTmn2kz+asM2NiLyahgQCaTzrw7FZpV3NHRoP0i5eTHcEnVXH/pz4v+CHt4v4LTh6KlXZFctYMr7CLUX+J6+vrRDs2uTfO7K3KBzT9I111vqivLz/ANOR3O+CbK7/ANufA/KpqPv4HcPQB2yKxLR9JwtScIHWpfuKvc/SmNAaLrgCwvuvCK6m7/258D8Eix/8p3n8EOvfwFw9BP3TiM3ovjhzLso3WE/nuQBsCvJeW1CRYTRduEC2mnFFFN/8p3n8E4pvj+Ee/f5JKl5+B3D0Q/ceImYrcf4R1sPH3K9s/YteznNqRTl/8J0lw9CTN+llsqzaTteaPl8EuafDg2menEiRJgyLxOt4S+338D3Q9FfFYKrSYS/nWsLRTzOYd9yJO8x5KnQwUtzBxdfQC49EyY3dIBbuzMGCX4d7HNbWbkObRj7Gm/Tc6L8CULkRDK1TC1hAqtdTuBLH9vVGnEJqSUZSXjnA9sZYoxX0jGp8U2E2g6kTBuYvBtF1cxuEdTe5jtWmD+usX71SfT3b1tKEZL8M4pR2ss1aZeS9zruMnXenVTI7iUlG2SIyapqgWG5UcZWLoBPR39Y3/FWXUL9/gszaFcBpPgOO5w8YXdLCwdCM2o11WqGD0iQ3qzGxPUN/ilXLalW38Ntm/cYNe0wT2uT4MZKdSqdT9Gw/aeOm7uZI7ajSlhaLskhpOYxYE2Fz4mPZKTxgzkywzFP1zGeoxHYNyLTxDvWPiUBtB3qu8CjUqDvVd4FQ7M2FdiXesfEqBxDvWPiUzqTvVd4FNzZH1T4FQxYJiub9I+JUhWPH3oLFKUrAmHn9Snzn9SotcpntU2xjZ+zzT871Dw/skxo4EqYaLdA68UrALTxcGzGqw3aTpjIO4JsPRn/LHiVdo4Y26DB4rCUo+jRJj0MU46s9y0cU/wCkd0frH/uVelSPBvcD8VdxrSKjvvO3faXPJp8G0eALnNv0QkxwBPRBTEaqUX/XUs2ykIuHAJwR6o0Txok3d2obERdHAJNc3gFNzbDq+KhCLYMQeOHkpc4OCgQnPvQA4eOCRf1KbaRIm0HrAuNde1NzJ+z7TfilkCOaUz29WqI2geLfab8VN2HMfVtf0m9+/sTaYEiMzA7eOife0+Ej8IWRytoxUp4pluduSN1ZkB/j0XfiW3gaJktJbDhHpN11G/j5EodfC87Qq0Prj6Wn/qUwSR+JhcO0BbaM9k03w8FIjygazEUqeMaLvAbUA3PuAOqGtjuHFctSqTqLixW9yDxArNq4J5/iAmn9l9pd3RPcVjYhpY85gBqHDSCLLt6f7W9J+OP0fH7cC1ldSG5sFJTA4aJLpMCGIq+fR/MHxXNbTrycvC5HA6R5LXxtWJJ0AyzvtcO/XFZWzfTdXcJFMZ4OhfMU2+0R3NKay79FvCJbSEOZQH+WMp66roNTwMM/AncBMDRogdcanvMnvQcExxLn3JHecztD73fhVinh3x6DvZPwUMxYmBHahN3qYKzJYijYazXu4DKO11v+3N4KtmVkmGNG9xLu4dFvnnQBHwU2t6x4KDCdwnuVxrn7qY71nKTQUBZTHX3NVpmFBHov8AEWk6razRvVzpRchYS1GaRiVqeBb6h73IzMKBHRG/eSjZk+8LFzkzRRQag0AHREY4T4ILDY/renabqPJQYP7NEbaD/pH/edu+0q9OmToCbbgSrG0KTucf0Xek76p9YooYA1LlSa+6CTcqTTp3fmkwRYn3pTqhZreClIv2KQDOOqjnKiCog6JAEzJB6G42SbvQBYBljh6pDu49E+eVAJ+KJhDLo9YFveRbwMHuQA7TwTAK1EY644KvKK0pAORHVBV2pWLXsqt1MO/GDfzE96qvomxMXA1IG6Rv4EI1FhLHNMSOkIcDpZwgHhB/CqptUPgwtqU24XGioyWsfFWmRuDtW/hdmb3Lb5X4dr8mJYBkrtzGNzgIA7wJ7QVU23huewjou/DnnBxNN0CoO45Xd7kfkXWGIw1XBPN2jnaZPAXAHefAldnc+2Gt6w/wBP45NFlUc2XObbh1pK5naLOADhYg6g8CkvQOfac3tEzDe53WAbHvmVtu5JuOzm1+cDHOJqCnlJNWARTAdNuiKjhYzmCyuSGzf2zG0qbrtLszyf5bBmdPaBHaV3f/qbiQ4tFKrUpNoAAtpnIx2YCWjKRBa1trbncVdYomT8nmGjWt6sx7XC39Me0VYwrZdfQXPYBJ8hHeq2Yklx1JJPaVap2YT6xy9whzv/AA81k+TIRcSSTqSSe0pFyiCmeIsoaESpsJt+rrZqYEOkAkOYIjc7KOlHAg5j19utHZAbzgLogS4g7w0FxHeAR3q3hq30tOHXLwT43Wcm+ClRKjhBwJUnENGiXPCCcw6kCrVaS0ZgubLeS8I0qDibx+tVO5I8UChVEaqYqibHcs5RLTD7km6yoB2iedVIyxQpkjUADU8ApirBOUb9Tr/+e6/Wo1DDGD1pce4loHdH9RQGO1vvTQ2Gzk6km2+6PtD+I/77veVTc/r3KztB3Tf993vUgPz5k5ukOvXuOoTOpiA5ukgGdWngfjvVdxv3BHwRlwbud0fEwD3GD3J8jQ/FSA0QWHXsU2u0WbQBWiyjKYFRdqigCSma4yFDN+vJInVAi03ojMRJnojs3nq/PsSxfpHg6HD8QzfnCjiD0WH7Pnmd8R4qNQyxh4S0/wDcPe4fhTrAyJKkwoScW3qaAv1OlTB3tsey5H/l7IQMPWLXA8PPiO8KeCdct9YR+LVviRHeqztSnXkDRo1BSqyRmZvHr03iCO9p81zTHHAY0XkUniDufSddp7CCCuv2HiabS2pUvzdojNINxI7M49lR/wDV3Y7Syji6Q6OUU3wNxux0cNR4Lt6WKdp8PwO64Od27WbUxFV+TLL3WkcY89e9JZuG2phixvPUqj3gAFwdAIFh5AJL0I6W1JLwS9Urck9oswjTVc7K+qCxkhx+jBHOHogkSQGj7rk3KfbYxHoxDjmMBwJMDNOYDfoRxdKw9pVA+rlZdjAKVPra22b8Tpd+JEqYd0wGuhogW1jU95k96bk6MJc2CareIsQ31RH4tXeZI7goYekWnM5pAF72mLgd5gd6GCe9ZEhAVAlLMoJUBcoGGPM65WjvOY+TY/EnYSwEk9IiGjgDq48Lads8JiamVjBFzLpN4k5RbjDZv6yr3OtybpYAKRbVSYwesPP4ITmlSa1SBaZkH1/Io9Ksy/Td7lQDEgxQ4p+Ro2WYltrowxbL9IarBy3TjTvWb0UVvZ05xjHMEEEsmR9kwZ7iTPaFWZjW/oLGZVLS0gwReR4fkpZ2O16BvoJae7VvdPYELQQ3Nmx+2N47ur4o+0Mc3nH9L67lz7qPBzSPvAeToKNj6J5x92jpu+u33Ayj6dUHcZqftjZ9Lgj4XGNaC/NpMdbotHZr3DiueOUG5zdQkDvJv3Ad6i6sT5gAaDsCHoIFqM6FuMbOqJTxAgarnadd02KPSqvj0t6yloUNTOha/XVIlZlAmbuPgrIHWVi40WmWgkShQnOgU4GW6LwW5XGBMg+qfgbA9gO6FIMID2Ebg4deXr3jKXFVGnVWMLicrmgkFuhncDYwd1iU00MEHJg5M5haS06gkeCSkArHxoYKPjSJDho6/jeO45h+FVWlWmQ5mWYINptIN47iP600A+Bf0spNndE9U+iewGF2ezKQxWBdh6ljlcy/BsEO/CSD+Erhm0r+k3xW7+/qtLI5gpkFsEwTeTmm+pJk/eW2lLY7YI8rxlJ1N7qbxDmOLSOsGE69ROxMBifp69QsqvAzgaZmjKT3xm70l6MeshXIds8dwoiXcBb7xsPzd+FO0KTrNa38R7Tp5R7RTAq2c5MC6JKE0qYKkkTipNEkACSbDrQ5uj4R8OzeqC7vAt5wkAXFHpujQdEHiGgNB8AFBhuq+ZSBSaAKSpByrkqYKVAHz2SlAc5OClQBs+qWewugSn4IoA7naX3fmVBvxTH8gmabJpAybij7Qd9JU++VWJ1R8f8AxH/fKAIAXF0ekxvHeq7YtqrVJg9VyiTGkHoFgj4K4yq28e5VadJsej5q02m2TYaLlnRqiwyuLIoqWQKbRbRHMLF0WhZ08m6g0iFKbqQHadPBO5DOinmRQw+KMkO9ZoPf6LvFwJ70GVOZpdbHeTh8WnxQpQwJsKnmVcORgnQEqovO43VjDnMxzN46Y7h0h7N/wBViej2fr9dqfDVcrg4biD2oQE21RF9UyJXoAOMXGo7CJHkQklsC2efOYCS4vbcz9b5UhRH8xv8AV8qDCdq9ls5yxzI/mN/r+VOaY9dvg75UGVBxTJoMKQ9dvg75VMw0EBwJMCwdoJJ1A3hvmgNTAqbGTlSBshgqRSESJupAoQKk1AE3FMCm3pxokApUsyGVPemA7nfl7kmlJwuVOnSJ/XWkBE70fHO+kf8AeKk3BOM3VzGbM+kfLvrKHqRS5KUWzMznin5w8Vqt2a0EI7cG0fVWb1ojUGY7HHjvV6k10i5WgKQ3AahEI07lk9W/BewrUmmN6uMPuUA39WU2f28VjKVlpUMHaj8kg7r9ybeEg3VKwJHfp4BOHW/4UJ0TtSsA9GoBmBmHDcBYyCDHd5pwGes72R2esq5Kdrt36ugCxkp+s72B86kOb9d3sD51VD0muKdgXmCn67r29EfOh5WD6z/YHzoQJhPUBN+KmwNHDbVyNDWuMDSabTvn10lmZUlpvYHCpAKGZIL1aMAkKJCYOTSkwCFO2meCJhyBqrArNA8Fm5V4HRWbh3cFM4V/DzHxVhldvmndX4So3v0FIAzBu6vEIrMA47wiU3W0KO09SlzY0kVxgL+kNFP9gt6XuRHOKc6f2Ub5ex7UC/YRa6N+yMB1SI9FTdqk5P2OkTbQZJtx3dasUwANPLrVdm9EkQs7bYzZ/dL7kFp6IcI3zuFtfiq2125KzwTeRPVIBjtug4eu/K+Hus0bzYZh1qO0zNV95uDf7oTltrCHkjUq6KXO71Wiyk0WChrAFhrtbjcpOdb0hb9cFXGp7lKLFKsjYfNfUeCdjo3j9dyrnRPvU0FhnOHrb+H9k5eJ9L9eCAW69tlItNk6QEw63pfrwT576+/4IbWGSmgpYAM52tx5/BJjvtDz+CbIogWRVgEJHEefwTz1jz+CHCcN7fBKgLIjiPP4JZ90/regCeB4J2tM6e5DQBA9OllPBJFgcGa56vYb8E4rO4D2G/BbbKVQwwZLwNArw2I4ww123A+roCL6jcL9y9upGLcVycuap+z7LfgkKh4t9lvwW27Ysj+NYzHR3SQDrviU/wC4xH8XyKe2QXExRX62+w35VMYn7vsM+C2KuyabWBxqON+HnPs+0FWqU6Q+sSocWWkmVG48jc3/AG6fypztJ32P9un8qtyz7XkmqO018ku2wpFZu1H/AGf9tnyqQ2o/i3/bZ8q28NsbFPY1zaFRzTdpjUbiEWpsDG6fs9TwR2hWvZgfvN3Fv+2z5UhtN/Fv+2z5Vsv2BjD/AJFTy+Kccn8aSPoak/hH5o7QWjGO1HcR7DflSG1XesPYb8qLiM7XFrnEPaSCOBFiPJA5x8znMo7IWgzNsPH1x7DflT/vp/rj2G/KhUqzwZzE7r316ks0mTO+YMAjWIiw1S7I9xdwu2HltU59KYPoj+ZTHDrKDV20/MfpN+9o+CPgnnLW6cfRGAZ15xhgXM2BWM9r5kOcO/8AOyXYQ9xqM224f5h8P7In+IX/AMx367ll03XBOYka9KxI0JEIvPWi4I4GN29H06FuL45QP/mP80v8QVP5jz4qmyRaTHa74qba5bv3/a8xKX06HZZPKOrufU8SmHKWtuc/2nfktDCbCxtRjXspSxwBaQ5okHSxeiHkxj4jmbf6jfnT+nXoNy9mV/iTEfzH+05MOUGIP+e/xctX/C+PiOZ7ucZ86Q5L4/Tme7nGfOn2PwLcvZmDbdc/9TV7i/4Jv3pUOuJreL1Xxb6jHOpvs5pLXCZggwRre6GcQ+InzPxS7LHgvt2gd+IqnvqfFGZtgt0q1f6llmq/j5n4pOrOi5Seg3ywwbTdvu/m1fF6d/KB382p29NYTqzo9LuQ31XbyfNT9KvY7R0J5Qu1NWqe94Tt5QuB/i1vF65tzz+p+KYvP6CPpV7Czp/8RP8A5lbxekub/aH8Uk/pY+/kNw4xR1A0U3bbdHogG95PZ/ZJJdTMGk+SWH2o5zQ0N6VgOFrATKH+9nXaQLdtiEySi3ZdIVTaOduU+U8RPuC2eSuy6OJLm1K5punotDC4uESTm0b1d6SSuOQaoq4jA5MnTkVGl7dZDMxa0ndJyzG5VqbTokkmwR67gKQFFlL1WBh/CMp9yK2pIA7j3WKSSdkJAWuaBfv7RY+5Bw4fMWsfdb8vNJJJPINYPK6pJqPJGriZ7z8VClSdJt7kklJaC0cI8uIy+YRKeEqSRHHeEkkrwVQXCUHgvBabtcBGXWQRMnTsuq9PA1MxGXzHxSSSsB8PsysSYZbtb8VLD7Krkkhn9Tfikkk5cjosUNmVySQye9vHtUKWxq5JIp2v9ZvxSSTsD0rkpQd+y0gRduYa7g8xoeELUoUSRp58Uklak6MnFWPQok3A6/FQoUSTIHXr/dJJPcG1HnPKPk/iH4upUp05Y6HDpNGrRNi6dZWZT5NYtxkUbffp/Mkks3I1SJU+TWMcZFG33qf5uT0+S+McZFC336fzpJKdzHRNvJPGuMjD2+/T+dSZyRxrjIw5j/UpfOnSRuYUKnyOxzjIw1h9ul7i9Epcice4g/s9h/8AJTv/AFpJJqQUN/grH7sPb/UpfOkkkmI//9k=");
            background-size:cover;
            background-repeat:no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .register-container h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group input:focus {
            border-color: #6e8efb;
            outline: none;
        }
        .register-btn {
            width: 100%;
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .register-btn:hover {
            background: #007bff;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .login-link a {
            color: #6e8efb;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }

        /* Popup styles */
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            display: none; /* Ẩn mặc định */
            z-index: 1000;
            text-align: center;
            animation: fadeIn 0.3s ease-out;
        }
        .popup h2 {
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }
        .popup p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #666;
        }
        .popup button {
            background: #6e8efb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .popup button:hover {
            background: #5a76d7;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none; /* Ẩn mặc định */
        }
    </style>
    <script>
        // Hiển thị popup
        window.onload = function () {
            const message = "<?php echo isset($message) ? $message : ''; ?>";
            const popup = document.querySelector('.popup');
            const overlay = document.querySelector('.overlay');
            const popupMessage = document.querySelector('.popup p');

            if (message === "success") {
                popupMessage.textContent = "Đăng ký thành công!";
                popup.style.display = 'block';
                overlay.style.display = 'block';
            } else if (message) {
                popupMessage.textContent = message;
                popup.style.display = 'block';
                overlay.style.display = 'block';
            }
        };

        // Ẩn popup khi bấm nút
        function closePopup() {
            document.querySelector('.popup').style.display = 'none';
            document.querySelector('.overlay').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="overlay"></div>
    <div class="popup">
        <h2>Thông Báo</h2>
        <p></p>
        <button onclick="closePopup()">Đóng</button>
    </div>
    <div class="register-container">
        <h1>Đăng Ký</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="firstname">Họ</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="lastname">Tên</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="username">Tên Đăng Nhập</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật Khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="register-btn">Đăng Ký</button>
        </form>
        <div class="login-link">
            <p>Đã có tài khoản? <a href="/admin">Đăng nhập ngay</a></p>
        </div>
    </div>
</body>
</html>
