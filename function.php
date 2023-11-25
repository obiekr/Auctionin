<?php

// fetch data user berdasarkan username
function getUser($username, $conn)
{
        $userResult = $conn->prepare("SELECT * FROM user WHERE username = ?");
        $userResult->execute([$username]);
        $user = $userResult->fetch();
        return $user;
}

// fetch seluruh data produk yang ada di website ini
function getAllProduct($conn)
{
        $result = $conn->prepare("SELECT * FROM product");
        $result->execute();
        $data = $result->fetchAll();
        return $data;
}

// fetch data produk tertentu sesuai id nya
function getProduct($itemid, $conn)
{
        $result = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
        $result->execute([$itemid]);
        $item = $result->fetch();
        return $item;
}

// seandainya ada yang mau ngebid 
function bidRequestHandler($conn, $user, $item, $bidPrice)
{
        date_default_timezone_set('Asia/Jakarta');
        // kalau harga baru lebih besar atau sama dengan 10000 DAN balance nya cukup DAN belum expired
        if ($bidPrice >= $item["product_price"] + 10000 & $user["balance"] >= $bidPrice & $temp = strtotime($item["product_expired"]) >= time()) {
                // keep timer to 5 minutes if someone bid 
                if ($temp - time() <= 300) {
                        $item["product_expired"] = date("Y-m-d H:i:s", (300 - $temp % 60) + time());
                }

                $olduserid = $item["highest_bidder_id"];
                $oldprice = $item["product_price"];
                $userResult = $conn->prepare("SELECT balance FROM user WHERE user_id = ?");
                $userResult->execute([$olduserid]);
                $olduserBalance = $userResult->fetch();
                $olduserBalance = $olduserBalance["balance"];


                $sql = "UPDATE product SET highest_bidder_id = ?,
                    product_price = ?,
                    product_expired = ?
                WHERE product_id = ?;";
                $result = $conn->prepare($sql);
                $result->execute([(int)$user["user_id"], (int)$bidPrice, $item["product_expired"], (int)$item["product_id"]]);

                // deduct user balance
                $sql = "UPDATE user SET balance = ?
                WHERE user_id = ?;";
                $result = $conn->prepare($sql);
                $result->execute([$user["balance"] - $bidPrice, $user["user_id"]]);

                // return old user balance
                $result = $conn->prepare($sql);
                $result->execute([(int)$oldprice + (int)$olduserBalance, $olduserid]);

                return true;
        } else {
                return false;
        }
}

// taruh barang di auction
function auctionItem($conn, $user, $post, $foto)
{
        date_default_timezone_set('Asia/Jakarta');
        $sql = "INSERT INTO product(
                product_id, 
                product_name, 
                product_price, 
                product_image, 
                product_desc, 
                product_register, 
                product_expired, 
                sentToAddress,
                isOwnerPaid,
                owner_id, 
                highest_bidder_id) VALUES (NULL, ?, ?, ?, ?, NOW(), ?, false, false, ?, ?)";
        $name = $post["product_name"];
        $price = $post["product_price"];
        $image = $foto;
        $desc = $post["product_desc"];
        $expired = $post["expired"];
        $owner = $user["user_id"];


        if ($expired == "24h") {
                $expired = date("Y-m-d H:i:s", time() + 3600 * 24);
        } elseif ($expired == "1w") {
                $expired = date("Y-m-d H:i:s", time() + 3600 * 24 * 7);
        } elseif ($expired == "1min") { // demo
                $expired = date("Y-m-d H:i:s", time() + 60);
        } elseif ($expired == "2w") {
                $expired = date("Y-m-d H:i:s", time() + 3600 * 24 * 14);
        } elseif ($expired == "1M") {
                $expired = date("Y-m-d H:i:s", time() + 3600 * 24 * 7 * 4);
        } else {
                $expired = date("Y-m-d H:i:s", time() + 3600 * 24 * 7 * 12);
        }

        try {
                // initial value highest bidder adalah owner
                $result = $conn->prepare($sql);
                $result->execute([$name, $price, $image, $desc, $expired, $owner, $owner]);
                return true;
        } catch (Exception) {
                return false;
        }
}

// deposit uang
function deposit($conn, $user, $deposit)
{
        $balance = $user["balance"] + $deposit;
        $result = $conn->prepare("UPDATE user SET balance = ? WHERE user_id = ?");
        $result->execute([(int)$balance, $user["user_id"]]);
}

// fetch data list barang yang menang bid
function getWonProduct($conn, $user)
{
        $sql = "SELECT * FROM product WHERE product_expired < NOW() AND highest_bidder_id = ?";
        $result = $conn->prepare($sql);
        $result->execute([$user["user_id"]]);
        $data = $result->fetchAll();
        return $data;
}

// ganti status product ke lagi dikirim
function sendProduct($conn, $item)
{
        try {
                $sql = "UPDATE product SET sentToAddress = true
                        WHERE product_id = ?";
                $result = $conn->prepare($sql);
                $result->execute([$item["product_id"]]);
                return true;
        } catch (Exception) {
                return false;
        }
}

// buat periksa apakah ada item yg baru expired? kalo ada kasih duitnya ke owner
// function dipanggil setiap kali load page, kecuali login & register
function updateBalance($conn)
{
        $ownerSql = "SELECT *
        FROM user 
            INNER JOIN product ON user.user_id=product.owner_id
        WHERE product.product_id = ?";
        $paySql = "UPDATE user SET balance = ? WHERE user_id = ?";
        $updatePaidSql = "UPDATE product SET isOwnerPaid = true WHERE product_id = ?";

        $data = getAllProduct($conn);
        foreach ($data as $item) {
                // kalo owner blm dibayar DAN udah expired
                if (!$item["isOwnerPaid"] & strtotime($item["product_expired"]) - time() < 0) {
                        $userResult = $conn->prepare($ownerSql);
                        $userResult->execute([$item["owner_id"]]);
                        $owner = $userResult->fetch();
                        $ownerBalance = $owner["balance"];
                        $conn->prepare($paySql)->execute([$item["product_price"]+$ownerBalance, $item["owner_id"]]);
                        $conn->prepare($updatePaidSql)->execute([$item["product_id"]]);
                }
        }
}
