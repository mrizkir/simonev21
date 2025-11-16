class TahunAnggaranModel {
  final String value;
  final String text;

  TahunAnggaranModel({
    required this.value,
    required this.text,
  });

  factory TahunAnggaranModel.fromJson(Map<String, dynamic> json) {
    // Handle jika value/text adalah integer atau string
    String getValue() {
      if (json['value'] != null) {
        return json['value'].toString();
      }
      if (json['ta'] != null) {
        return json['ta'].toString();
      }
      return '';
    }

    String getText() {
      if (json['text'] != null) {
        return json['text'].toString();
      }
      if (json['ta'] != null) {
        return json['ta'].toString();
      }
      return '';
    }

    return TahunAnggaranModel(
      value: getValue(),
      text: getText(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'value': value,
      'text': text,
    };
  }
}

