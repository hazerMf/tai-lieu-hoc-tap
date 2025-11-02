# --- Packet Type Constants ---
JOIN_REQ = 1
PASS_REQ = 2
PASS_RESP = 3
PASS_ACCEPT = 4
DATA = 5
TERMINATE = 6
REJECT = 7


class ctrl_msg_packet:
    def __init__(self, header, len_payload):
        self.header = header
        self.len_payload = len_payload

    def encode(self):
        enc_header = self.header.to_bytes(2, 'big')
        enc_len_payload = self.len_payload.to_bytes(4, 'big')

        enc = enc_header + enc_len_payload
        return enc


class pass_resp_packet:
    def __init__(self, header, len_payload, password):
        self.header = header
        self.len_payload = len_payload
        self.password = password

    def encode(self):
        enc_header = self.header.to_bytes(2, 'big')
        enc_password = self.password.encode()
        enc_len_payload = len(enc_password).to_bytes(4, 'big')

        enc = enc_header + enc_len_payload + enc_password
        return enc


class data_packet:
    def __init__(self, header, len_payload, pkt_id, data):
        self.header = header
        self.len_payload = len_payload
        self.pkt_id = pkt_id
        self.data = data

    def encode(self):
        enc_header = self.header.to_bytes(2, 'big')
        enc_data = self.data.encode()
        enc_len_payload = len(enc_data).to_bytes(4, 'big')
        enc_pkt_id = self.pkt_id.to_bytes(4, 'big')

        enc = enc_header + enc_len_payload + enc_pkt_id + enc_data

        return enc


class terminate_packet:
    def __init__(self, header, len_payload, digest):
        self.header = header
        self.len_payload = len_payload
        self.digest = digest

    def encode(self):
        enc_header = self.header.to_bytes(2, 'big')
        enc_len_payload = self.len_payload.to_bytes(4, 'big')

        enc = enc_header + enc_len_payload + self.digest

        return enc


def decode_packet(packet):
    header = int.from_bytes(packet[0:2], 'big')
    len_payload = int.from_bytes(packet[2:6], 'big')

    if ((header == JOIN_REQ) or
        (header == PASS_REQ) or
        (header == PASS_ACCEPT) or
        (header == REJECT)):
        packet = ctrl_msg_packet(header, len_payload)
    elif (header == PASS_RESP):
        password = packet[6:].decode()
        packet = pass_resp_packet(header, len_payload, password)
    elif (header == DATA):
        pkt_id = int.from_bytes(packet[6:10], 'big')
        data = packet[10:].decode()
        packet = data_packet(header, len_payload, pkt_id, data)
    elif (header == TERMINATE):
        digest = packet[6:]
        packet = terminate_packet(header, len_payload, digest)
    else:
        print("Unknown packet.")

    return packet


def reserve_mapping(num):
    if (num == JOIN_REQ):
        return "JOIN_REQ"
    elif (num == PASS_REQ):
        return "PASS_REQ"
    elif (num == PASS_RESP):
        return "PASS_RESP"
    elif (num == PASS_ACCEPT):
        return "PASS_ACCEPT"
    elif (num == REJECT):
        return "REJECT"
    elif (num == DATA):
        return "DATA"
    elif (num == TERMINATE):
        return "TERMINATE"
    else:
        return "UNKNOWN"
